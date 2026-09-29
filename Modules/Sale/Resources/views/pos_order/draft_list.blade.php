<div class="modal fade admin-query" id="draft_list">
    <div class="modal-dialog modal_1000px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('sale.Draft List') }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <table class="table Crm_table">
                    <thead>
                        <tr>
                            <th scope="col">{{__('common.Sl')}}</th>
                            <th scope="col">{{__('common.Date')}}</th>
                            <th scope="col">{{__('common.Customer')}}</th>
                            <th scope="col">{{__('sale.User')}}</th>
                            <th scope="col">{{__('product.QTY')}}</th>
                            <th scope="col">{{__('common.Total Amount')}}</th>
                            <th scope="col">{{__('common.Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $key=> $order)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{showDate($order->created_at)}}</td>
                                <td>{{@$order->customer->name}}</td>
                                <td>{{@$order->user->name}}</td>
                                <td>{{$order->total_quantity}}</td>
                                <td>{{single_price($order->payable_amount)}}</td>
                                <td>
                                    <a onclick="demo({{ $order->id }})" class="primary-btn"><i class="ti-plus"></i> {{ __('sale.Add to POS') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
