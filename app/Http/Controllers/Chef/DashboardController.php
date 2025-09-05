<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Model\ChefBranch;
use App\Model\Branch;
use App\Model\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the KDS dashboard for chefs
     */
    public function dashboard()
    {
        // Redirect to KDS controller
        return redirect()->route('chef.kds.dashboard');
    }
}
