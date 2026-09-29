@extends('backEnd.master')
@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('product.Stock Adjustments')}} </h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <div id="item_list_tbl">
                        @include('inventory::stock_adjustment.components.list')
                    </div>
                    <!-- table-responsive -->
                    {{-- <div class="">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th scope="col">{{ __('common.Sl') }}</th>
                                <th scope="col">{{__('quotation.Date')}}</th>
                                <th scope="col">{{__('product.Branch/Warehouse')}}</th>
                                <th scope="col">{{__('product.Reference No')}}</th>
                                <th scope="col">{{__('product.Recovery Amount')}}</th>
                                <th scope="col">{{__('product.Created User')}}</th>
                                <th scope="col">{{__('product.Updated User')}}</th>
                                <th scope="col">{{__('common.Status')}}</th>
                                <th scope="col">{{__('common.Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($items as $key=> $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ showDate($item->date) }}</td>
                                    <td>{{@$item->adjustable->name}}</td>
                                    <td>{{$item->ref_no}}</td>
                                    <td>{{single_price($item->recovery_amount)}}</td>
                                    <td>{{ userName($item->created_by) }}</td>
                                    <td class="text-center">{{ ($item->updated_by) ? userName($item->updated_by) : "X" }}</td>
                                    <td>
                                        @if (@$item->status != 1)
                                            <h6><span class="badge_4">{{__('product.Pending')}}</span></h6>
                                        @else
                                            <h6><span class="badge_1">{{__('product.Approved')}}</span></h6>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown CRM_dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('common.Select')}}
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                                                @if ($item->status != 1)
                                                     @if(permissionCheck('stock_adjustment.approve'))
                                                    <a onclick="approve_modal('{{route('stock_adjustment.approve', $item->id)}}')" class="dropdown-item edit_brand">{{__('sale.Approve')}}</a>
                                                    @endif
                                                     @if(permissionCheck('stock_adjustment.edit'))
                                                    <a href="{{route('stock_adjustment.edit',$item->id)}}" class="dropdown-item" type="button">{{__('common.Edit')}}</a>
                                                    @endif
                                                     @if(permissionCheck('stock_adjustment.destroy'))
                                                    <a onclick="confirm_modal('{{route('stock_adjustment.destroy', $item->id)}}')" class="dropdown-item">{{__('common.Delete')}}</a>
                                                    @endif
                                                @endif
                                                 @if(permissionCheck('stock_adjustment.show'))
                                                <a href="{{route('stock_adjustment.show',$item->id)}}" class="dropdown-item" type="button">{{__('common.View')}}</a>
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
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'stock_adjustments')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('quotation.Date') }}','date'],['3','{{ __('product.Branch/Warehouse') }}','showroom_or_wareHouse'],['4','{{ __('product.Reference No') }}','reference_no'],['5','{{ __('product.Recovery Amount') }}','recovery_amount'],['6','{{ __('product.Created User') }}','created_user'],['7','{{ __('product.Updated User') }}','updated_by'],['8','{{ __('common.Status') }}','status'],['9','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['stock_adjustments']) }}">
    
@include('backEnd.partials.delete_modal')
@include('backEnd.partials.approve_modal')
@endsection
@push("scripts")
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
@endpush
