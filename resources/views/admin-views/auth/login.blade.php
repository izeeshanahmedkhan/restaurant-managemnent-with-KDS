<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{translate('Admin')}} | {{translate('Login')}}</title>

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
                <img width="310" src="{{ $logo }}" alt="{{ translate('logo') }}">
                <h2 class="title">{{translate('your')}} <span class="c1 d-block text-capitalize">{{translate('Kitchen')}}</span> <strong class="text--039D55 c1 text-capitalize">{{translate('your_food')}}....</strong></h2>
            </div>
        </div>

        <div class="auth-wrapper-right">
            <div class="auth-wrapper-form">
                    <form class="" id="form-id" action="{{route('admin.auth.login')}}" method="post">
                        @csrf
                        <div class="auth-header">
                            <div class="mb-5">
                                <h2 class="title">{{translate('sign_in')}}</h2>
                                <div class="text-capitalize">{{translate('welcome_back')}}</div>
                            </div>
                        </div>

                        <div class="js-form-message form-group">
                            <label class="input-label text-capitalize" for="signinSrEmail">{{translate('your')}} {{translate('email')}}</label>

                            <input type="email" class="form-control form-control-lg" name="email" id="signinSrEmail"
                                tabindex="1" placeholder="{{translate('email@address.com')}}" aria-label="email@address.com"
                                required data-msg="{{ translate('Please enter a valid email address') }}">
                        </div>

                        <div class="js-form-message form-group">
                            <label class="input-label" for="signupSrPassword" tabindex="0">
                                <span class="d-flex justify-content-between align-items-center">
                                {{translate('password')}}
                                </span>
                            </label>

                            <div class="input-group input-group-merge">
                                <input type="password" class="js-toggle-password form-control form-control-lg"
                                    name="password" id="signupSrPassword" placeholder="{{translate('8+ characters required')}}"
                                    aria-label="8+ characters required" required
                                    data-msg="{{ translate('Your password is invalid. Please try again.') }}"
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

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="termsCheckbox"
                                    name="remember">
                                <label class="custom-control-label text-muted" for="termsCheckbox">
                                    {{translate('remember_me')}}
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-lg btn-block btn-primary" id="signInBtn">{{translate('sign_in')}}</button>
                    </form>

                    <div class="border-top border-primary pt-5 mt-10">
                        <div class="row">
                            <div class="col-10">
                                <span>{{translate('Email : admin@example.com')}}</span><br>
                                <span>{{translate('Password : 12345678')}}</span>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-primary px-3 copy-cred"><i class="tio-copy"></i>
                                </button>
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
        toastr.error('{{$error}}', Error, {
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

        $(".re-captcha").click(function() {
            re_captcha();
        });

        $(".copy-cred").click(function() {
            copy_cred();
        });
    });
</script>

    <script>
        "use strict";

        function copy_cred() {
            $('#signinSrEmail').val('admin@example.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('{{\App\CentralLogics\translate("Copied successfully!")}}', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
