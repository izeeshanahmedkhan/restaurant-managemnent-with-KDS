<div id="headerMain">
    <header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper">
                @php($restaurantLogo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
                <a class="navbar-brand" href="{{route('chef.dashboard')}}" aria-label="">
                    <img class="navbar-brand-logo" style="object-fit: contain;"
                         onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                         src="{{asset('storage/app/restaurant/'.$restaurantLogo)}}" alt="{{ translate('logo') }}">
                    <img class="navbar-brand-logo-mini" style="object-fit: contain;"
                         onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                         src="{{asset('storage/app/restaurant/'.$restaurantLogo)}}" alt="{{ translate('logo') }}">
                </a>
            </div>

            <div class="navbar-nav-wrap-content-left d-xl-none">
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-3">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                       data-placement="right" title="Collapse"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                       data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                       data-toggle="tooltip" data-placement="right" title="Expand"></i>
                </button>
            </div>

            <div class="navbar-nav-wrap-content-right">
                <ul class="navbar-nav align-items-center flex-row">




                    <li class="nav-item ml-4">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper media gap-2" href="javascript:;"
                               data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                <div class="media-body d-flex align-items-end flex-column">
                                    <span class="card-title h5">{{auth('chef')->user()->f_name}} {{auth('chef')->user()->l_name}}</span>
                                    <span class="card-text fz-12 font-weight-bold">{{translate('Chef')}}</span>
                                </div>
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                         onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                         src="{{asset('storage/app/chef')}}/{{auth('chef')->user()->image}}"
                                         alt="Image Description">
                                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                            </a>

                            <div id="accountNavbarDropdown"
                                 class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account width-14rem">
                                <div class="dropdown-item-text">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-sm avatar-circle mr-2">
                                            <img class="avatar-img"
                                                 onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                                 src="{{asset('storage/app/chef')}}/{{auth('chef')->user()->image}}"
                                                 alt="{{ translate('chef image') }}">
                                        </div>
                                        <div class="media-body">
                                            <span class="card-title h5">{{auth('chef')->user()->f_name}} {{auth('chef')->user()->l_name}}</span>
                                            <span class="card-text">{{auth('chef')->user()->email}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{route('chef.dashboard')}}">
                                    <span class="text-truncate pr-2" title="Dashboard">{{translate('dashboard')}}</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="javascript:" onclick="Swal.fire({
                                    title: '{{translate('Do you want to logout ?')}}',
                                    showDenyButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: '#FC6A57',
                                    cancelButtonColor: '#363636',
                                    confirmButtonText: `{{translate('Yes')}}`,
                                    cancelButtonText: `{{translate('No')}}`,
                                    }).then((result) => {
                                    if (result.value) {
                                    location.href='{{route('chef.auth.logout')}}';
                                    } else{
                                        Swal.fire({
                                        title: '{{translate("Canceled")}}',
                                        confirmButtonText: '{{translate("Okay")}}',
                                        })
                                    }
                                    })">
                                    <span class="text-truncate pr-2" title="Sign out">{{translate('sign_out')}}</span>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>
