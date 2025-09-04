<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Customer Register - FoodKing</title>

    @php($icon = \App\Model\BusinessSetting::where(['key' => 'fav_icon'])->first()?->value??'')
    <link rel="shortcut icon" href="">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/app/restaurant/' . $icon ?? '') }}">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">
</head>

<body>
<main id="content" role="main" class="main">
    <div class="auth-wrapper">
        <div class="auth-wrapper-left">
            <div class="auth-left-cont">
                @php($logoName = \App\CentralLogics\helpers::get_business_settings('logo'))
                @php($logo = \App\CentralLogics\helpers::onErrorImage($logoName, asset('storage/restaurant') . '/' . $logoName, asset('public/assets/admin/img/logo.png'), 'restaurant/'))
                <img width="310" src="{{ $logo }}" alt="logo">
                <h2 class="title">Your <span class="c1 d-block text-capitalize">Kitchen</span> <strong class="text--039D55 c1 text-capitalize">Your Food....</strong></h2>
            </div>
        </div>

        <div class="auth-wrapper-right">
            <div class="auth-wrapper-form">
                <form class="" id="form-id" action="{{route('register.post')}}" method="post">
                    @csrf
                    <div class="auth-header">
                        <div class="mb-5">
                            <h2 class="title">Create Account</h2>
                            <div class="text-capitalize">Join us to continue to checkout</div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="js-form-message form-group">
                                <label class="input-label text-capitalize" for="f_name">First Name</label>
                                <input type="text" class="form-control form-control-lg" name="f_name" id="f_name"
                                    tabindex="1" placeholder="First Name" aria-label="First Name"
                                    value="{{ old('f_name') }}" required data-msg="Please enter your first name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="js-form-message form-group">
                                <label class="input-label text-capitalize" for="l_name">Last Name</label>
                                <input type="text" class="form-control form-control-lg" name="l_name" id="l_name"
                                    tabindex="2" placeholder="Last Name" aria-label="Last Name"
                                    value="{{ old('l_name') }}" required data-msg="Please enter your last name">
                            </div>
                        </div>
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label text-capitalize" for="email">Your Email</label>
                        <input type="email" class="form-control form-control-lg" name="email" id="email"
                            tabindex="3" placeholder="email@address.com" aria-label="email@address.com"
                            value="{{ old('email') }}" required data-msg="Please enter a valid email address">
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label text-capitalize" for="phone">Phone Number</label>
                        <input type="tel" class="form-control form-control-lg" name="phone" id="phone"
                            tabindex="4" placeholder="Phone Number" aria-label="Phone Number"
                            value="{{ old('phone') }}" required data-msg="Please enter your phone number">
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label" for="password" tabindex="0">
                            <span class="d-flex justify-content-between align-items-center">
                            Password
                            </span>
                        </label>

                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg"
                                name="password" id="password" placeholder="8+ characters required"
                                aria-label="8+ characters required" required
                                data-msg="Your password is invalid. Please try again."
                                data-hs-toggle-password-options='{
                                    "target": "#changePassTarget",
                                    "defaultClass": "tio-hidden-outlined",
                                    "showClass": "tio-visible-outlined",
                                    "classChangeTarget": "#changePassIcon"
                                    }'>
                            <div id="changePassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label" for="password_confirmation" tabindex="0">
                            <span class="d-flex justify-content-between align-items-center">
                            Confirm Password
                            </span>
                        </label>

                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg"
                                name="password_confirmation" id="password_confirmation" placeholder="Confirm Password"
                                aria-label="Confirm Password" required
                                data-msg="Password confirmation does not match."
                                data-hs-toggle-password-options='{
                                    "target": "#changePassTarget2",
                                    "defaultClass": "tio-hidden-outlined",
                                    "showClass": "tio-visible-outlined",
                                    "classChangeTarget": "#changePassIcon2"
                                    }'>
                            <div id="changePassTarget2" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changePassIcon2" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-lg btn-block btn-primary" id="signUpBtn">Create Account</button>
                </form>

                <div class="border-top border-primary pt-5 mt-10">
                    <div class="row">
                        <div class="col-12 text-center">
                            <p class="mb-2">Already have an account? <a href="{{ route('login') }}" class="text-primary">Sign In</a></p>
                            <p class="mb-0"><a href="{{ route('shop.index') }}" class="text-muted">Back to Shop</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        "use strict";

        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', 'Error', {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<script>
    "use strict";

    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>