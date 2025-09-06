<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">
                    <!-- Logo -->
                    @php($restaurant_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
                    <a class="navbar-brand" href="{{route('admin.dashboard')}}" aria-label="Front">
                        <img class="navbar-brand-logo" style="object-fit: contain;"
                             onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/restaurant/'.$restaurant_logo)}}"
                             alt="Logo">
                        <img class="navbar-brand-logo-mini" style="object-fit: contain;"
                             onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/restaurant/'.$restaurant_logo)}}" alt="Logo">
                    </a>
                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align" data-template="<div class=&quot;tooltip d-none d-sm-block&quot; role=&quot;tooltip&quot;><div class=&quot;arrow&quot;></div><div class=&quot;tooltip-inner&quot;></div></div>" data-toggle="tooltip" data-placement="right" title="" data-original-title="Expand"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->

                    <div class="navbar-nav-wrap-content-left d-none d-xl-block">
                        <!-- Navbar Vertical Toggle -->
                        <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                            <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                            <i class="tio-last-page navbar-vertical-aside-toggle-full-align"></i>
                        </button>
                        <!-- End Navbar Vertical Toggle -->
                    </div>
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content">
                    <div class="sidebar--search-form py-3">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input" placeholder="Search Menu...">
                        </div>
                    </div>

                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
{{--                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['dashboard_management']))--}}
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.dashboard')}}" title="{{translate('Dashboards')}}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('dashboard')}}
                                    </span>
                            </a>
                        </li>
{{--                        @endif--}}
                        <!-- End Dashboards -->

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['pos_management']))

                            <!-- POS -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/pos/*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-shopping nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('POS')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/pos*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/pos')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.pos.index')}}"
                                           title="{{translate('pos')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('New Sale')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/pos/orders')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.pos.orders')}}" title="{{translate('orders')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                {{translate('orders')}}
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::Pos()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- End POS -->
                        @endif

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['order_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">{{translate('order')}} {{translate('management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <!-- Offline payment verification functionality removed -->

                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/orders/list/*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-shopping-cart nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('order')}}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/order*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/orders/list/all')?'active':''}}">
                                        <a class="nav-link" href="{{route('admin.orders.list',['all'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                <span>{{translate('all')}}</span>
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/pending')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['pending'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                <span>{{translate('pending')}}</span>
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'pending'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/confirmed')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['confirmed'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                {{translate('confirmed')}}
                                                    <span class="badge badge-soft-success badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'confirmed'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/processing')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['processing'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                    {{translate('processing')}}
                                                    <span class="badge badge-soft-warning badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'processing'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/out_for_delivery')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['out_for_delivery'])}}"
                                           title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                    {{translate('out_for_delivery')}}
                                                    <span class="badge badge-soft-warning badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'out_for_delivery'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/delivered')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['delivered'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                    {{translate('delivered')}}
                                                    <span class="badge badge-soft-success badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'delivered'])->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/returned')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['returned'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                    {{translate('returned')}}
                                                    <span class="badge badge-soft-danger badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'returned'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/failed')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['failed'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                {{translate('failed_to_deliver')}}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'failed'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{Request::is('admin/orders/list/canceled')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['canceled'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                {{translate('canceled')}}
                                                    <span class="badge badge-soft-dark badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'canceled'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{Request::is('admin/orders/list/schedule')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['schedule'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                {{translate('scheduled')}}
                                                    <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where('delivery_date','>',\Carbon\Carbon::now()->format('Y-m-d'))->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- KDS (Kitchen Display System) -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/kds/*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                   href="{{route('admin.kds.dashboard')}}" title="{{translate('Kitchen Display System')}}">
                                    <i class="tio-monitor nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('Kitchen Display')}}
                                    </span>
                                </a>
                            </li>
                            <!-- End KDS -->

                            <!-- End Pages -->
                        @endif

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['product_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">{{translate('product')}} {{translate('management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>


                            <!-- Pages -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/category*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                >
                                    <i class="tio-category nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('category')}} {{translate('setup')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/category*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/category/add')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.category.add')}}"
                                           title="{{translate('add new category')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('category')}}</span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{Request::is('admin/category/add-sub-category')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.category.add-sub-category')}}"
                                           title="{{translate('add new sub category')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('sub_category')}}</span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                            <!-- End Pages -->


                            <!-- Pages -->
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/addon*') ||Request::is('admin/product*') || Request::is('admin/attribute*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                >
                                    <i class="tio-premium-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('product')}} {{translate('setup')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/product*') || Request::is('admin/addon*') || Request::is('admin/attribute*')?'block':'none'}}">
{{--                                    <li class="nav-item {{Request::is('admin/attribute*')?'active':''}}">--}}
{{--                                        <a class="nav-link " href="{{route('admin.attribute.add-new')}}"--}}
{{--                                           title="{{translate('Add product attribute')}}">--}}
{{--                                            <span class="tio-circle nav-indicator-icon"></span>--}}
{{--                                            <span--}}
{{--                                                class="text-truncate">{{translate('Product_Attributes')}}</span>--}}
{{--                                        </a>--}}
{{--                                    </li>--}}
                                    <li class="nav-item {{Request::is('admin/addon*')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.addon.add-new')}}"
                                           title="{{translate('add addon')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{translate('Product_Addon')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/product/add-new') ?'active':'' }}">
                                        <a class="nav-link " href="{{route('admin.product.add-new')}}" title="{{translate('add')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('Product Add')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/product/list') || Request::is('admin/product/edit*') ?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.product.list')}}" title="{{translate('product_list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('product_list')}}</span>
                                        </a>
                                    </li>
                                    <!-- Product import/export functionality removed -->
                                    <!-- Reviews functionality removed -->
                                </ul>
                            </li>
                            <!-- End Pages -->
                        @endif




{{--                        REPORT & ANALYTICS MANAGEMENT--}}
                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['report_and_analytics_management']))
                            <li class="nav-item">
                                <small class="nav-subtitle"
                                       title="{{translate('report and analytics')}}">{{translate('report_and_analytics')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <!-- Pages -->
                                    <li class="nav-item {{Request::is('admin/report/earning')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.report.earning')}}">
                                            <i class="tio-chart-pie-1 nav-icon"></i>
                                            <span
                                                class="text-truncate">{{translate('earning')}} {{translate('report')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/report/order')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.report.order')}}"
                                        >
                                            <i class="tio-chart-bar-2 nav-icon"></i>
                                            <span
                                                class="text-truncate">{{translate('order')}} {{translate('report')}}</span>
                                        </a>
                                    </li>
{{--                                    <li class="nav-item {{Request::is('admin/report/deliveryman-report')?'active':''}}">--}}
{{--                                        <a class="nav-link " href="{{route('admin.report.deliveryman_report')}}"--}}
{{--                                        >--}}
{{--                                            <i class="tio-chart-bar-3 nav-icon"></i>--}}
{{--                                            <i class="tio-chart-donut-2 nav-icon"></i>--}}
{{--                                            <span--}}
{{--                                                class="text-truncate">{{translate('DeliveryMan Report')}}</span>--}}
{{--                                        </a>--}}
{{--                                    </li>--}}
                                    <li class="nav-item {{Request::is('admin/report/product-report')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.report.product-report')}}"
                                        >
                                            <i class="tio-chart-bubble nav-icon"></i>
                                            <span
                                                class="text-truncate">{{translate('product')}} {{translate('report')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/report/sale-report')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.report.sale-report')}}">
                                            <i class="tio-chart-bar-1 nav-icon"></i>
                                            <span class="text-truncate">{{translate('sale')}} {{translate('report')}}</span>
                                        </a>
                                    </li>
{{--                                </ul>--}}
{{--                            </li>--}}
                            <!-- End Pages -->
                        @endif


                        <!-- User Management -->
                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))
                            <li class="nav-item {{(Request::is('admin/employee*') || Request::is('admin/custom-role*'))?'scroll-here':''}}">
                                <small class="nav-subtitle">{{translate('user_management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/transaction') || Request::is('admin/customer/list') || Request::is('admin/customer/view*') || Request::is('admin/customer/settings')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-poi-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('customer')}}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/customer/transaction') || Request::is('admin/customer/list')  || Request::is('admin/customer/view*')  || Request::is('admin/customer/settings')?'block':''}}; top: 831.076px;">
                                    <li class="nav-item {{Request::is('admin/customer/list') || Request::is('admin/customer/view*') ? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('admin.customer.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('list')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>


                            <!-- End Pages -->
                        @endif

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))
                        @endif

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))
                            @if(auth('admin')->user()->admin_role_id == 1)
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/custom-role*') || Request::is('admin/employee*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('Employees')}}">
                                        <i class="tio-incognito nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('Employees')}}
                                        </span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub " style="display: {{Request::is('admin/custom-role*') || Request::is('admin/employee*')?'block':''}}">

                                        <li class="nav-item {{Request::is('admin/custom-role*')? 'active': ''}}">
                                            <a class="nav-link" href="{{route('admin.custom-role.create')}}" title="{{translate('Employee Role Setup')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                                    {{translate('Employee Role Setup')}}</span>
                                            </a>
                                        </li>

                                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/employee*')?'active':''}}">
                                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('Employee Setup')}}">
                                                <span class="tio-user mr-2"></span>
                                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                                    {{translate('Employee Setup')}}
                                                </span>
                                            </a>
                                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/employee*')?'block':''}}">
                                                <li class="nav-item {{Request::is('admin/employee/add-new')?'active':''}}">
                                                    <a class="nav-link " href="{{route('admin.employee.add-new')}}" title="{{translate('add new')}}">
                                                        <span class="tio-circle nav-indicator-icon"></span>
                                                        <span class="text-truncate">{{translate('add new')}}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item {{Request::is('admin/employee/list')?'active':''}}">
                                                    <a class="nav-link" href="{{route('admin.employee.list')}}" title="{{translate('List')}}">
                                                        <span class="tio-circle nav-indicator-icon"></span>
                                                        <span class="text-truncate">{{translate('list')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                            @endif

                        @endif

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))

                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/kitchen*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                   href="javascript:">
                                    <i class="tio-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('chef')}}
                                        </span>
                                    <label class="badge badge-danger">{{translate('addon')}}</label>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/kitchen*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/kitchen/add-new')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.kitchen.add-new')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('add_new')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/kitchen/list')?'active':''}}">
                                        <a class="nav-link" href="{{route('admin.kitchen.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('List')}}</span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                        @endif

                        <!-- User Management  End-->




                        <!-- BRANCH -->

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['system_management']))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('system')}} {{translate('setting')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- Business_Setup -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/restaurant*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.business-settings.restaurant.restaurant-setup')}}">
                                <i class="tio-settings nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Business_Setup')}}</span>
                            </a>
                        </li>
                        <!-- END Business_Setup -->


                        <!--BRANCH SETUP -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/branch*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                   href="javascript:">
                                    <i class="tio-shop nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('Branch_Setup')}}
                                        </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/branch*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/branch/add-new')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.branch.add-new')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('add_new')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/branch/list')?'active':''}}">
                                        <a class="nav-link" href="{{route('admin.branch.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('List')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <!--END BRANCH SETUP -->

                        <!-- PAGE SETUP -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/web-app/social-media') || Request::is('admin/business-settings/page-setup/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-pages nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Page & Media')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/business-settings/web-app/social-media') || Request::is('admin/business-settings/page-setup*')?'block':'none'}}">
                                <!-- Page Setup -->
                                <li class="nav-item {{Request::is('admin/business-settings/page-setup*')?'active':''}}">
                                    <a class="nav-link "
                                       href="{{route('admin.business-settings.page-setup.about-us')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Page_Setup')}}</span>
                                    </a>
                                </li>

                                <!-- Social media functionality removed -->
                            </ul>
                        </li>
                        @endif

                        <!-- Third party functionality removed -->
                        <!--END SYSTEM SETTINGS -->
                        <li class="nav-item pt-10">
                            <div class=""></div>
                        </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>


{{--<script>
    $(document).ready(function () {
        $('.navbar-vertical-content').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 'slow');
    });
</script>--}}

@push('script_2')
    <script>
        $(window).on('load' , function() {
            if($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        //Sidebar Menu Search
        var $rows = $('.navbar-vertical-content  .navbar-nav > li');
        $('#search-bar-input').keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $rows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });

    </script>
@endpush

