<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Chef Login - Kitchen Display System</title>

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
                @php($logo = \App\Model\BusinessSetting::where(['key' => 'logo'])->first()?->value??'')
                <img width="310" src="{{ asset('storage/app/restaurant/' . $logo) }}" alt="logo">
                <h2 class="title">Your <span class="c1 d-block text-capitalize">Kitchen</span> <strong class="text--039D55 c1 text-capitalize">Display System</strong></h2>
            </div>
        </div>

        <div class="auth-wrapper-right">
            <div class="auth-wrapper-form">
                <form class="" id="form-id" action="{{route('chef.auth.login')}}" method="post">
                    @csrf
                    <div class="auth-header">
                        <div class="mb-5">
                            <h2 class="title">Chef Login</h2>
                            <div class="text-capitalize">Welcome to Kitchen Display System</div>
                        </div>
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label text-capitalize" for="signinSrEmail">Your Email</label>

                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               name="email" id="signinSrEmail" value="{{ old('email') }}"
                               tabindex="1" placeholder="chef@example.com" aria-label="chef@example.com"
                               required data-msg="Please enter a valid email address">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label" for="signupSrPassword" tabindex="0">
                            <span class="d-flex justify-content-between align-items-center">
                            Password
                            </span>
                        </label>

                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg @error('password') is-invalid @enderror"
                                name="password" id="signupSrPassword" placeholder="8+ characters required"
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
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="termsCheckbox"
                                name="remember">
                            <label class="custom-control-label text-muted" for="termsCheckbox">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-lg btn-block btn-primary" id="signInBtn">Sign In</button>
                </form>

                <div class="border-top border-primary pt-5 mt-10">
                    <div class="row">
                        <div class="col-10">
                            <h6 class="mb-3">How to Create Chef Accounts</h6>
                            <p class="mb-2"><strong>Admin Panel:</strong> Go to Kitchen Management → Add New Chef</p>
                            <p class="mb-2"><strong>Branch Panel:</strong> Go to Kitchen Management → Add New Chef</p>
                            <p class="mb-0 text-muted">Chef accounts are created by administrators and assigned to specific branches.</p>
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

        $(".copy-cred").click(function() {
            copy_cred();
        });
    });
</script>

    <script>
        "use strict";

        function copy_cred() {
            var copyText = "Admin Panel: Kitchen Management → Add New Chef\nBranch Panel: Kitchen Management → Add New Chef";
            navigator.clipboard.writeText(copyText).then(function() {
                toastr.success('Instructions copied to clipboard!', Success, {
                    CloseButton: true,
                    ProgressBar: true
                });
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</body>
</html>
