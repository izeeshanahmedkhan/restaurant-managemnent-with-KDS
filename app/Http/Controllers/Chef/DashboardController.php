<?php

namespace App\Http\Controllers\Chef;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

class DashboardController extends Controller
{
    public function __construct(
        private Order  $order,
    )
    {}


    private function getChefBranchId()
    {
        $chefBranch = \App\Model\ChefBranch::where('user_id', auth('chef')->user()->id)->first();
        if (!$chefBranch) {
            \Log::error('Chef branch not found for user: ' . auth('chef')->user()->id);
            return null;
        }
        return $chefBranch->branch_id;
    }


    private function getAllTimeOrderStatistics(): array
    {
        $branchId = $this->getChefBranchId();
        
        if (!$branchId) {
            \Log::error('No branch ID found for chef: ' . auth('chef')->user()->id);
            return [
                'pending' => 0,
                'confirmed' => 0,
                'processing' => 0,
                'out_for_delivery' => 0,
                'delivered' => 0,
                'canceled' => 0,
                'returned' => 0,
                'failed' => 0,
                'all' => 0,
                'total_earning' => 0,
                'total_delivery_fee' => 0,
            ];
        }
        
        \Log::info('Chef branch ID: ' . $branchId);

        $pending = $this->order->where(['order_status' => 'pending', 'branch_id' => $branchId])->count();
        $confirmed = $this->order->where(['order_status' => 'confirmed', 'branch_id' => $branchId])->count();
        $processing = $this->order->where(['order_status' => 'processing', 'branch_id' => $branchId])->count();
        $outForDelivery = $this->order->where(['order_status' => 'out_for_delivery', 'branch_id' => $branchId])->count();
        $delivered = $this->order->where(['order_status' => 'delivered', 'branch_id' => $branchId])->count();
        $all = $this->order->where('branch_id', $branchId)->count();
        $canceled = $this->order->where(['order_status' => 'canceled', 'branch_id' => $branchId])->count();
        $returned = $this->order->where(['order_status' => 'returned', 'branch_id' => $branchId])->count();
        $failed = $this->order->where(['order_status' => 'failed', 'branch_id' => $branchId])->count();
        $totalEarning = $this->order->where(['order_status' => 'delivered', 'branch_id' => $branchId])->sum('order_amount');
        $totalDeliveryFee = $this->order->where(['order_status' => 'delivered', 'branch_id' => $branchId])->sum('delivery_charge');
        
        \Log::info('Order counts - Pending: ' . $pending . ', Confirmed: ' . $confirmed . ', Delivered: ' . $delivered . ', All: ' . $all);

        return [
            'pending' => $pending,
            'confirmed' => $confirmed,
            'processing' => $processing,
            'out_for_delivery' => $outForDelivery,
            'delivered' => $delivered,
            'canceled' => $canceled,
            'returned' => $returned,
            'failed' => $failed,
            'all' => $all,
            'total_earning' => $totalEarning,
            'total_delivery_fee' => $totalDeliveryFee,
        ];
    }


    public function dashboard(): Renderable
    {
        Helpers::update_daily_product_stock();

        $data = $this->getAllTimeOrderStatistics();

        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');

        $earning = [];
        $earningData = $this->order->where([
            'order_status' => 'delivered',
            'branch_id' => $this->getChefBranchId()
        ])->select(
            DB::raw('IFNULL(sum(order_amount),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month')
        )
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupby('year', 'month')->get()->toArray();

        for ($inc = 1; $inc <= 12; $inc++) {
            $earning[$inc] = 0;
            foreach ($earningData as $match) {
                if ($match['month'] == $inc) {
                    $earning[$inc] = Helpers::set_price($match['sums']);
                }
            }
        }

        $orderStatisticsChart = [];
        $orderStatisticsChartData = $this->order->where([
            'order_status' => 'delivered',
            'branch_id' => $this->getChefBranchId()
        ])
            ->select(
                DB::raw('(count(id)) as total'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupby('year', 'month')->get()->toArray();

        for ($inc = 1; $inc <= 12; $inc++) {
            $orderStatisticsChart[$inc] = 0;
            foreach ($orderStatisticsChartData as $match) {
                if ($match['month'] == $inc) {
                    $orderStatisticsChart[$inc] = $match['total'];
                }
            }
        }

        $donut = [];
        $donutData = $this->order->where('branch_id', $this->getChefBranchId())->get();
        $donut['pending'] = $donutData->where('order_status', 'pending')->count();
        $donut['ongoing'] = $donutData->whereIn('order_status', ['confirmed', 'processing', 'out_for_delivery'])->count();
        $donut['delivered'] = $donutData->where('order_status', 'delivered')->count();
        $donut['canceled'] = $donutData->where('order_status', 'canceled')->count();
        $donut['returned'] = $donutData->where('order_status', 'returned')->count();
        $donut['failed'] = $donutData->where('order_status', 'failed')->count();

        $data['recent_orders'] = $this->order->latest()
            ->where('branch_id', $this->getChefBranchId())
            ->take(5)
            ->get();

        return view('chef-views.dashboard', compact('data', 'earning', 'orderStatisticsChart', 'donut'));
    }


    public function orderStatisticsData(): array
    {
        $today = session()->has('order_statistics_date') ? session('order_statistics_date') : now()->format('Y-m-d');
        $thisMonth = \Carbon\Carbon::parse($today)->format('m');
        $thisYear = \Carbon\Carbon::parse($today)->format('Y');

        $pending = $this->order->where(['order_status' => 'pending', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->count();

        $confirmed = $this->order->where(['order_status' => 'confirmed', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->count();

        $processing = $this->order->where(['order_status' => 'processing', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->count();

        $outForDelivery = $this->order->where(['order_status' => 'out_for_delivery', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->count();

        $delivered = $this->order->where(['order_status' => 'delivered', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->count();

        $all = $this->order->where('branch_id', $this->getChefBranchId())
            ->whereDate('created_at', $today)->count();

        $canceled = $this->order->where(['order_status' => 'canceled', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->count();

        $returned = $this->order->where(['order_status' => 'returned', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->count();

        $failed = $this->order->where(['order_status' => 'failed', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->count();

        $totalEarning = $this->order->where(['order_status' => 'delivered', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->sum('order_amount');

        $totalDeliveryFee = $this->order->where(['order_status' => 'delivered', 'branch_id' => $this->getChefBranchId()])
            ->whereDate('created_at', $today)->sum('delivery_charge');

        return [
            'pending' => $pending,
            'confirmed' => $confirmed,
            'processing' => $processing,
            'out_for_delivery' => $outForDelivery,
            'delivered' => $delivered,
            'all' => $all,
            'canceled' => $canceled,
            'returned' => $returned,
            'failed' => $failed,
            'total_earning' => $totalEarning,
            'total_delivery_fee' => $totalDeliveryFee,
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orderStats(Request $request): JsonResponse
    {
        session()->put('order_statistics_date', $request['date']);
        $data = self::orderStatisticsData();
        return response()->json([
            'view' => view('chef-views.partials._dashboard-order-stats', compact('data'))->render()
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function orderStatistics(): JsonResponse
    {
        $from = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

        $data = [];
        $period = CarbonPeriod::create($from, $to);
        foreach ($period as $date) {
            $data[] = $this->order->where(['order_status' => 'delivered', 'branch_id' => $this->getChefBranchId()])
                ->whereDate('created_at', $date)->count();
        }

        return response()->json([
            'view' => view('chef-views.partials._dashboard-order-statistics', compact('data'))->render()
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function earningStatistics(): JsonResponse
    {
        $from = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

        $data = [];
        $period = CarbonPeriod::create($from, $to);
        foreach ($period as $date) {
            $data[] = $this->order->where(['order_status' => 'delivered', 'branch_id' => $this->getChefBranchId()])
                ->whereDate('created_at', $date)->sum('order_amount');
        }

        return response()->json([
            'view' => view('chef-views.partials._dashboard-earning-statistics', compact('data'))->render()
        ]);
    }
}