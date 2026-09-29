<div class="dropdown CRM_dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button"
            id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false"> {{trans('common.Select')}}
    </button>
    <div class="dropdown-menu dropdown-menu-right"
         aria-labelledby="dropdownMenu2">
         @if ($orders->is_approved == 0)
            @if (permissionCheck('conditional.sale.approve') && $orders->is_approved == 0)
                <a onclick="approve_modal('{{route('sale_pos.approve', $orders->id)}}')"
                   class="dropdown-item edit_brand">{{__('sale::sale.approve')}}</a>
            @endif
         @endif
        @if ($orders->status != 1 && $orders->is_approved == 1)
            <a href="{{route('sale.payment',$orders->id)}}" class="dropdown-item"
               type="button">{{__('sale::sale.payment')}}</a>
        @endif
        @if ($orders->is_approved == 1)
            @if (permissionCheck('sale.return') && $orders->return_status != 1)
                <a href="{{route('sale.return',$orders->id)}}" class="dropdown-item"
                   type="button">{{__('sale::sale.sale_return')}}</a>
            @endif
            @if(permissionCheck('return.sale.approve') && $orders->return_status == 0 && $orders->items->sum('return_quantity') > 0)
                <a onclick="approve_modal('{{route('return.sale.approve', $orders->id)}}')" class="dropdown-item edit_brand">{{__('sale::sale.return_approve')}}</a>
            @endif
        @endif

        @if(permissionCheck('sale.show'))
            <a href="{{route('sale.show',$orders->id)}}" class="dropdown-item" type="button">{{__('sale::sale.details')}}</a>
        @endif
        <a href="{{route('sale.pdf',$orders->id)}}" class="dropdown-item" type="button">{{__('sale::sale.download')}}</a>
        <a href="{{route('sale.challan_pdf',$orders->id)}}" class="dropdown-item" type="button">{{__('sale::sale.challan_download')}}</a>
        @if (permissionCheck('sale.clone'))
            <a href="{{route('sale.clone',$orders->id)}}" class="dropdown-item" type="button">{{__('sale::sale.clone_to_sale')}}</a>
        @endif
        @if (permissionCheck('sale.convertTosale'))
            <a href="{{route('sale.convertTosale', $orders->id)}}" class="dropdown-item" type="button">{{__('sale::sale.clone_to_quotation')}}</a>
        @endif
        @if(permissionCheck('sale.delete') && $orders->is_approved != 1)
             <a onclick="confirm_modal('{{route('sale.delete', $orders->id)}}')" class="dropdown-item edit_brand">{{trans('common.Delete')}}</a>
        @endif
        @if ($orders->is_approved == 1)
            <a href="{{route('sale.show',$orders->id)}}" class="dropdown-item edit_brand">{{trans('common.Print')}}</a>
        @endif
    </div>
</div>
