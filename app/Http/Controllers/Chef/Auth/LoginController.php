<?php

namespace App\Http\Controllers\Chef\Auth;

use App\CentralLogics\helpers;
use App\Http\Controllers\Controller;
use App\User;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:chef', ['except' => ['logout']]);
    }

    // Captcha method removed - same as admin login

    /**
     * @return Renderable
     */
    public function login(): Renderable
    {
        $logoName = Helpers::get_business_settings('logo');
        $logo = Helpers::onErrorImage($logoName, asset('storage/restaurant') . '/' . $logoName, asset('public/assets/admin/img/logo.png'), 'restaurant/');
        return view('chef-views.auth.login', compact('logo'));
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

        // Captcha removed for chef login - same as admin

        if (auth('chef')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => 'kitchen',
            'is_active' => 1
        ], $request->remember)) {
            return redirect()->route('chef.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors([translate('Credentials does not match.')]);
    }

    /**
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        auth()->guard('chef')->logout();
        return redirect()->route('chef.auth.login');
    }
}
