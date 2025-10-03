@php
    $sideBarLinks = json_decode($sidenav);
@endphp

<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img src="{{ siteLogo() }}" alt="image"></a>
        </div>
        <div class="sidebar__menu-wrapper">
            <ul class="sidebar__menu">

                @foreach ($sideBarLinks as $key => $data)
                    @php
                        $show = true;
                        if (@$data->admin_only) {
                            $show = auth()->guard('admin')->id() == 1;
                        }
                        if (!$show) {
                            continue;
                        }

                        $hRouteName = @$data->route_name;

                        if (is_array(@$data->route_name)) {
                            foreach ($data->route_name as $route) {
                                $hRouteName = $route;
                                if (can($hRouteName)) {
                                    break;
                                }
                            }
                        }
                        $showHeader = @$data->header && ((!@$data->submenu && can(@$hRouteName)) || (@$data->submenu && can(array_column($data->submenu, 'route_name'))));
                    @endphp

                    @if ($showHeader)
                        <li class="sidebar__menu-header">{{ __($data->header) }}</li>
                    @endif

                    @if (@$data->submenu)
                        @can(array_column($data->submenu, 'route_name'))
                            <li class="sidebar-menu-item sidebar-dropdown">
                                <a href="javascript:void(0)" class="{{ menuActive(@$data->menu_active, 3) }}">
                                    <i class="menu-icon {{ @$data->icon }}"></i>
                                    <span class="menu-title">{{ __(@$data->title) }}</span>
                                    @foreach (@$data->counters ?? [] as $counter)
                                        @if ($$counter > 0)
                                            <span class="menu-badge menu-badge-level-one ms-auto">
                                                <i class="fas fa-bell text--warning ringing-bell"></i>
                                            </span>
                                        @break
                                    @endif
                                @endforeach
                            </a>
                            <div class="sidebar-submenu {{ menuActive(@$data->menu_active, 2) }}">
                                <ul>
                                    @foreach ($data->submenu as $menu)
                                        @php
                                            $submenuParams = null;
                                            if (@$menu->params) {
                                                foreach ($menu->params as $submenuParamVal) {
                                                    $submenuParams[] = array_values((array) $submenuParamVal)[0];
                                                }
                                            }
                                            $routeName = $menu->route_name;
                                        @endphp

                                        @can($menu->route_name)
                                            @php
                                                if (is_array($menu->route_name)) {
                                                    foreach ($menu->route_name as $route) {
                                                        $routeName = $route;
                                                        if (can($routeName)) {
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            <li class="sidebar-menu-item {{ menuActive(@$menu->menu_active) }} ">
                                                <a href="{{ route(@$routeName, $submenuParams) }}" class="nav-link">
                                                    <i class="menu-icon las la-dot-circle"></i>
                                                    <span class="menu-title">{{ __($menu->title) }}</span>
                                                    @php $counter = @$menu->counter; @endphp
                                                    @if (@$$counter)
                                                        <span class="menu-badge rounded-pill bg--info ms-auto">{{ @$$counter }}</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endcan
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endcan
                @else
                    @php
                        $mainParams = null;
                        if (@$data->params) {
                            foreach ($data->params as $paramVal) {
                                $mainParams[] = array_values((array) $paramVal)[0];
                            }
                        }
                        $routeName = $data->route_name;
                    @endphp

                    @can(@$data->route_name)
                        @php
                            if (is_array($data->route_name)) {
                                foreach ($data->route_name as $route) {
                                    $routeName = $route;
                                    if (can($routeName)) {
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <li class="sidebar-menu-item {{ menuActive(@$data->menu_active) }}">
                            <a href="{{ route(@$routeName, $mainParams) }}" class="nav-link ">
                                <i class="menu-icon {{ $data->icon }}"></i>
                                <span class="menu-title">{{ __(@$data->title) }}</span>
                                @php $counter = @$data->counter; @endphp
                                @if (@$$counter)
                                    <span class="menu-badge bg--info ms-auto">{{ @$$counter }}</span>
                                @endif
                            </a>
                        </li>
                    @endcan
                @endif
            @endforeach
        </ul>
    </div>
    <div class="version-info text-center text-uppercase">
        <span class="text--primary">{{ __(systemDetails()['name']) }}</span>
        <span class="text--success">@lang('V'){{ systemDetails()['version'] }} </span>
    </div>
</div>
</div>
<!-- sidebar end -->

@push('script')
<script>
    if ($('li').hasClass('active')) {
        $('.sidebar__menu-wrapper').animate({
            scrollTop: eval($(".active").offset().top - 320)
        }, 500);
    }
</script>
@endpush

@push('style')
<style>
    .ringing-bell {
        animation: ring 2s ease-in-out infinite;
        transform-origin: 50% 4px;
    }
    
    @keyframes ring {
        0% { transform: rotate(0); }
        1% { transform: rotate(30deg); }
        3% { transform: rotate(-28deg); }
        5% { transform: rotate(34deg); }
        7% { transform: rotate(-32deg); }
        9% { transform: rotate(30deg); }
        11% { transform: rotate(-28deg); }
        13% { transform: rotate(26deg); }
        15% { transform: rotate(-24deg); }
        17% { transform: rotate(22deg); }
        19% { transform: rotate(-20deg); }
        21% { transform: rotate(18deg); }
        23% { transform: rotate(-16deg); }
        25% { transform: rotate(14deg); }
        27% { transform: rotate(-12deg); }
        29% { transform: rotate(10deg); }
        31% { transform: rotate(-8deg); }
        33% { transform: rotate(6deg); }
        35% { transform: rotate(-4deg); }
        37% { transform: rotate(2deg); }
        39% { transform: rotate(-1deg); }
        41% { transform: rotate(1deg); }
        43% { transform: rotate(0); }
        100% { transform: rotate(0); }
    }
    
    .menu-badge-level-one {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .ringing-bell:hover {
        animation-duration: 0.5s;
    }
</style>
@endpush
