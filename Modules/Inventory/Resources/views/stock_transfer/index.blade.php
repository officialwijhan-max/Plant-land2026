@extends('backEnd.master')
@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('common.Stock Transfer')}} </h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <div id="item_list_tbl">
                        @include('inventory::stock_transfer.paginate.list')
                    </div>
                    <!-- table-responsive -->
                    {{-- <div class="">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transfers as $key=> $transfer)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{\Carbon\Carbon::parse($transfer->date)->isoformat('Do MMMM Y H:ss a')}}</td>
                                    <td>{{@$transfer->sendable->name}}</td>
                                    <td>{{@$transfer->receivable->name}}</td>
                                    <td>
                                        @if ($transfer->status == 1)
                                            <h6><span class="badge_1">{{__('product.Approved')}}</span></h6>
                                        @else
                                            <h6><span class="badge_4">{{__('common.Pending')}}</span></h6>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown CRM_dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                {{__('common.Select')}}
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                                                <a href="{{route('stock-transfer.show',$transfer->id)}}" class="dropdown-item" type="button">{{__('sale.Details')}}</a>
                                                @if (permissionCheck('stock-transfer.status') && $transfer->status == 0)
                                                    <a onclick="approve_modal('{{route('stock-transfer.status', $transfer->id)}}')"
                                                       class="dropdown-item" type="button">{{__('sale.Approve')}}</a>
                                                @endif
                                                @if (permissionCheck('stock-transfer.receive') && $transfer->status == 1 && !$transfer->received_at)
                                                    <a onclick="approve_modal('{{route('stock-transfer.receive', $transfer->id)}}')"
                                                       class="dropdown-item" type="button">{{__('product.Receive')}}</a>
                                                @endif
                                                @if(permissionCheck('stock-transfer.edit') && $transfer->status == 0)
                                                    <a href="{{route('stock-transfer.edit',$transfer->id)}}"
                                                       class="dropdown-item" type="button">{{__('common.Edit')}}</a>
                                                @endif
                                                @if(permissionCheck('stock-transfer.delete'))
                                                    <a onclick="confirm_modal('{{route('stock-transfer.delete', $transfer->id)}}')"
                                                       class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                                                @endif

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    @include('backEnd.partials.delete_modal')
    @include('backEnd.partials.approve_modal')
    <div class="showModalHideColumn"></div>

    @php
        if (request()->is('inventory/stock-transfer-recieve')) {
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'product_transfer_rcv_list')->first();
        } else {
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'product_sent_list')->first();
        }
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('quotation.Date') }}','date'],['3','{{ __('product.From') }}','from'],['4','{{ __('product.To') }}','to'],['5','{{ __('sale.Qty') }}','qty'],['6','{{ __('sale.Total Amount') }}','total_amount'],['7','{{ __('common.Status') }}','status'],['8','{{ __('common.Action') }}','action']]">
    
    @if (request()->is('inventory/stock-transfer-recieve'))
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['product_transfer_rcv_list']) }}">
    @else
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['product_sent_list']) }}">
    @endif
@endsection
@push("scripts")
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
@endpush

