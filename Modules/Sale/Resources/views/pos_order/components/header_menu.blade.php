<div class="container-fluid no-gutters">
    <div class="row">
        <div class="col-lg-12 p-0">
            <div class="header_iner d-flex justify-content-between align-items-center pos_header">
                <div class="small_logo_crm">
                    <a href="{{url('/login')}}">
                        <img src="{{asset(app('general_setting')->logo)}}" class="menu-logo" alt="">
                    </a>
                </div>
                <div class="header_middle ml-0 mr-auto ml-40">
                    <div class="select_style d-flex align-items-center">
                        @if (auth()->user()->role->type == "system_user" || permissionCheck('showroom.get_showroom_for_select'))
                            <select name="#" class="nice_Select select_showroom bgLess">
                                @foreach ($showrooms as $key => $showroom)
                                    <option value="{{ $showroom->id }}" @if ($showroom->id == session()->get('showroom_id')) selected @endif>{{ $showroom->name }}</option>
                                @endforeach
                            </select>
                        @elseif (auth()->user()->role->type == "regular_user")
                            <select name="#" class="nice_Select select_showroom bgLess">
                                @if (showroomName() != null)
                                    <option value="" selected>{{ showroomName() }}</option>
                                @else
                                    <option value="" selected>{{ trans("sale::sale.login_again") }}</option>
                                @endif
                            </select>
                        @endif
                        <p class="date_text nowrap">{{ Carbon\Carbon::now()->format(app('general_setting')->dateFormat->format)  }}</p>
                    </div>
                </div>
                <div class="header_right d-flex justify-content-between align-items-center">
                    <div class="header_notification_warp d-flex align-items-center">
                        <li>
                            <a class="gredient_hover" href="{{ route('home') }}">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li>
                            <a class="gredient_hover" href="{{ route('cashbook.index') }}">
                                <i class="fas fa-book-open"></i>
                            </a>
                        </li>
                        <li class="scroll_notification_list">
                            <a class="pulse theme_color bell_notification_clicker" href="#">
                                <!-- bell   -->
                                <i class="fa fa-bell"></i>

                                <!--/ bell   -->
                                <span class="notification_count">0</span>
                                <span class="notification_count_pulse pulse-ring"></span>
                            </a>
                            <!-- Menu_NOtification_Wrap  -->
                            <div class="Menu_NOtification_Wrap">
                                <div class="notification_Header">
                                    <h4>{{__('common.Notifications')}}</h4>
                                </div>
                                <div class="Notification_body">

                                    @if (app('business_settings')->where('type','system_notification')->where('status',1)->first())
                                        @foreach ($notifications as $key => $notification)

                                            <div class="single_notify d-flex align-items-center">

                                                <div class="notify_content">
                                                    <a href="{{$notification->url}}"
                                                       onclick="notification_remove({{$notification->id}},'{{$notification->url}}')">
                                                        <h5>{{$notification->type}} </h5></a>
                                                    <p >{{$notification->data}}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                                <div class="nofity_footer">
                                    <div class="submit_button text-center pt_20">
                                        <a href="{{route('all_notifications')}}"
                                           class="primary-btn radius_30px text_white  fix-gr-bg">{{__('product.See More')}}</a>
                                        @if(count($notifications))
                                            <span class="primary-btn radius_30px text_white notification_icon fix-gr-bg">{{__('common.Mark as seen')}}</span>
                                            @endif
                                    </div>
                                </div>
                            </div>
                            <!--/ Menu_NOtification_Wrap  -->
                        </li>
                    </div>
                    <div class="profile_info">
                        @if (Auth::user()->avatar != null)
                            <img src="{{ asset(Auth::user()->avatar) }}" alt="#">
                        @else
                            <img src="{{ asset('public/frontend/img/client_img.png') }}" alt="#">
                        @endif
                        <div class="profile_info_iner">
                            <p>{{trans('common.Welcome')}} {{ Auth::user()->role->name }}!</p>
                            <h5>{{ Auth::user()->name }}</h5>
                            <div class="profile_info_details">
                                <a href="{{route('company_info')}}">Company Info <i class="ti-user"></i></a>
                                @if (Auth::user()->staff)
                                    <a href="{{ route('profile_view') }}">{{ trans('common.Profile') }} <i
                                            class="ti-settings"></i></a>
                                @endif
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                    <i class="ti-shift-left"></i>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
