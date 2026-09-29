

<li class="{{ request()->routeIs('pos-order.index') ?'mm-active' : '' }}">
    <a href="javascript:void (0);" class="has-arrow" aria-expanded="false">
        <div class="nav_icon_small">
            <span class="fas fa-cash-register"></span>
        </div>
        <div class="nav_title">
            <span>{{__('sale.POS')}}</span>
        </div>
    </a>
    <ul>
        <li>
            @if(permissionCheck('pos-order.index'))
            <a href="{{route('pos-order.index')}}" class="{{request()->is('sales-section/pos/pos-order') ? 'active' : ''}}">{{__('sale.POS Sale')}}</a>
            @endif
            @if(permissionCheck('pos-order.products'))
            <a href="{{route('pos-order.products')}}">{{__('sale.POS')}}</a>
            @endif
        </li>
    </ul>
</li>

@if(permissionCheck('sale'))

    @php

        $sale = false;
        if(request()->is('sale/*') &&  !request()->routeIs('pos-order.index'))
        {
            $sale = true;
            $conditional = false;
        }
    @endphp


    <li class="{{ $sale ?'mm-active' : '' }}">
        <a href="javascript:void (0);" class="has-arrow" aria-expanded="false">
            <div class="nav_icon_small">
                <span class="fas fa-store"></span>
            </div>
            <div class="nav_title">
                <span>{{__('sale.Sale')}}</span>
            </div>
        </a>
        <ul>
            @if(permissionCheck('sale.index'))
                <li>
                    <a href="{{route('sale.index')}}"
                       class="{{request()->is('sale/sale') || request()->is('sale/sale/*') && !request()->is('sale/sale-return/*') ? 'active' : ''}}"> {{__('sale.Sale')}}</a>
                </li>
            @endif

            @if(moduleStatusCheck('ExtraUser'))
                @if(permissionCheck('affiliate.index'))
                    <li>
                        <a href="{{route('affiliate.index')}}"
                           class="{{request()->is('sale/affiliate') || request()->is('sale/affiliate/*') && !request()->is('sale/sale-return/*') ? 'active' : ''}}"> {{__('extrauser::extrauser.Affiliate')}}</a>
                    </li>
                @endif
                @if(permissionCheck('commissioner.index'))
                    <li>
                        <a href="{{route('commissioner.index')}}"
                           class="{{request()->is('sale/commissioner') || request()->is('sale/commissioner/*') && !request()->is('sale/sale-return/*') ? 'active' : ''}}"> {{__('extrauser::extrauser.Commissioner')}}</a>
                    </li>
                @endif
            @endif



            @if(permissionCheck('sale.return.index'))
                <li>
                    <a href="{{route('sale.return.index')}}"
                       class="{{request()->is('sale/sale-return/*') ? 'active' : ''}}"> {{__('sale.Sale Return')}}</a>
                </li>
            @endif

        </ul>
    </li>
@endif
