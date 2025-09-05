<?php

namespace App\Http\Controllers\Chef\Auth;

use App\CentralLogics\helpers;
use App\Http\Controllers\Controller;
use App\Model\ChefBranch;
use App\Model\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:chef', ['except' => ['logout']]);
    }


    /**
     * @return Renderable|\Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::guard('chef')->check()) {
            return redirect()->route('chef.dashboard');
        }
        
        return view('chef-views.auth.login');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // Captcha validation removed for chef login

        if (Auth::guard('chef')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $chef = Auth::guard('chef')->user();
            
            
            // Check if user is a kitchen staff
            if ($chef->user_type !== 'kitchen') {
                Auth::guard('chef')->logout();
                return back()->withErrors(translate('Access denied. This account is not authorized for kitchen access.'));
            }
            
            // Check if chef has any assigned branches
            $assignedBranches = ChefBranch::where('user_id', $chef->id)->pluck('branch_id');
            if ($assignedBranches->isEmpty()) {
                Auth::guard('chef')->logout();
                return back()->withErrors(translate('No branches assigned to this chef'));
            }


            return redirect()->route('chef.dashboard');
        }


        return back()->withErrors(translate('Credentials do not match or account has been suspended.'));
    }

    /**
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        Auth::guard('chef')->logout();
        return redirect()->route('chef.auth.login');
    }
}
