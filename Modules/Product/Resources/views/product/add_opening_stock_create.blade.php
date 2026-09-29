@extends('backEnd.master')
@section('mainContent')
   <section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
         <div class="row justify-content-center">
            <div class="col-12">
               <div class="box_header">
                  <div class="main-title d-flex">
                     <h3 class="mb-0 mr-30">{{__('common.Add Opening Stock')}}</h3>
                  </div>
               </div>
            </div>
            <div class="col-12">
               <div class="white_box_50px box_shadow_white">
                  <form action="{{route("purchase_order.add_to_stock")}}" method="POST" enctype="multipart/form-data">
                      @csrf
                      <div class="row">

                         <div class="col-lg-6">
                            <div class="primary_input mb-15">
                               <label class="primary_input_label" for="">{{__('product.Product')}} *</label>
                               <select class="single_select primary_singleSelect mb-15 product_sku_id" name="product_sku_id" id="product_sku_id" onchange="getProductDetails()">
                                   <option>{{__('quotation.Select Product')}}</option>
                               </select>
                               <span class="text-danger">{{$errors->first('product_sku_id')}}</span>
                            </div>
                         </div>

                          <div class="col-lg-6">
                              <div class="primary_input mb-15">
                                  <label class="primary_input_label" for="">{{ __('sale.Date') }} *</label>
                                  <div class="primary_datepicker_input">
                                      <div class="no-gutters input-right-icon">
                                          <div class="col">
                                              <div class="">
                                                  <input placeholder="Date"
                                                         class="primary_input_field primary-input date form-control"
                                                         id="startDate" type="text" name="stock_date"
                                                         value="{{date('m/d/Y')}}" autocomplete="off">
                                              </div>
                                          </div>
                                          <button class="" type="button">
                                              <i class="ti-calendar" id="start-date-icon"></i>
                                          </button>
                                      </div>
                                  </div>
                              </div>
                          </div>


                         <div class="col-lg-6">
                            <div class="primary_input mb-15">
                               <label class="primary_input_label" for="">{{__("common.Stock Quantity")}} *</label>
                               <div class="">
                                  <input type="number" min="0" step="0.01" name="stock_quantity" id="stock_quantity" required="1" class="primary_input_field" >
                               </div>
                            </div>
                         </div>
                         <div class="col-lg-6">
                            <div class="primary_input mb-15">
                               <label class="primary_input_label" for="">{{__("sale.Select Branch or WareHouse")}} *</label>
                               <select name="showroom" id="showroom" class="primary_select mb-15" >
                                   @if (Auth::user()->role->type == "system_user")
                                   <option selected disabled>{{__('common.Select')}}</option>
                                   @foreach($wareHouses as $warehouse)
                                       <option value="warehouse-{{$warehouse->id}}">{{$warehouse->name}}</option>
                                   @endforeach
                                   @foreach($showrooms as $showroom)
                                       <option value="showroom-{{$showroom->id}}" {{session()->get('showroom_id') == $showroom->id ? 'selected' : ''}}> {{$showroom->name}}</option>
                                   @endforeach
                                   @else
                                       <option value="showroom-{{ Auth::user()->staff->showroom_id }}" selected > {{showroomName()}}</option>
                                   @endif
                               </select>
                               <span class="text-danger">{{$errors->first('unit_type_id')}}</span>
                            </div>
                         </div>
                     </div>
                     <div class="row details">
                         <div class="col-lg-6">
                            <div class="primary_input mb-15">
                               <label class="primary_input_label" for="">{{__("common.Purchase Price")}} *</label>
                               <div class="">
                                  <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="" class="primary_input_field" >
                               </div>
                            </div>
                         </div>
                         <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{__("common.Selling Price")}} *</label>
                                <div class="">
                                   <input type="number" min="0" step="0.01" name="selling_price" id="selling_price" value="" class="primary_input_field" >
                                </div>
                            </div>
                         </div>
                     </div>
                     <div class="row d-none">
                         <div class="col-xl-6 col-lg-6 col-md-6">
                             <div class="primary_input mb-25">
                                 <label class="primary_input_label" for="">{{ __('common.Serial No') }} <small>({{ __('common.Manually') }})</small> </label>
                                 <div class="tagInput_field">
                                     <input class="sr-only" type="text" id="serial_no" name="serial_no" data-role="tagsinput" class="sr-only">
                                 </div>
                             </div>
                         </div>
                         <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="primary_input mb-15">
                               <label class="primary_input_label" for="">{{ __('common.Serial No') }} <small>({{ __('common.Automated via excel file') }}) <a href="{{ asset('uploads/sample.xlsx') }}" download>Sample File Download</a> </label>
                               <div class="primary_file_uploader">
                                  <input class="primary-input" type="text" id="placeholderFileOneName" placeholder="Browse file" readonly="">
                                  <button class="" type="button">
                                  <label class="primary-btn small fix-gr-bg" for="document_file_1">{{__("common.Browse")}} </label>
                                  <input type="file" class="d-none" accept=".xlsx, .xls, .csv" name="file" id="document_file_1">
                                  </button>
                               </div>
                            </div>
                         </div>
                     </div>
                     <div class="row">
                          <div class="col-12">
                             <div class="submit_btn text-center ">
                                <button class="primary-btn semi_large2 fix-gr-bg"><i
                                   class="ti-check"></i>{{__("common.Add Product")}}
                                </button>
                             </div>
                          </div>
                       </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid p-0 mt-3">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="box_header common_table_header">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('inventory.Opening Stock List') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table ">
                        <div id="item_list_tbl">
                            @include('product::product.paginate_component.opening_list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="showModalHideColumn"></div>
@php
    $employee_per = auth()->user()->user_col_permissions->where('table_name', 'opening_stock_add_list')->first();
@endphp
@if ($employee_per)
    @if ($employee_per->hide_column_no_by_self)
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
    @endif
@else
    <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
@endif
<input type="hidden" name="th_name" id="th_name" value="[['1','{{__('sale.Sl')}}','id'],['2','{{ __('sale.Date') }}','date'],['3','{{ __('common.Name') }}','name'],['4','{{ __('sale.SKU').'/'. __('common.Part Number')}}','SKU'],['5','{{ __('product.Brand') }}','brand'],['6','{{ __('product.Model') }}','model'],['7','{{ __('inventory.Branch') }}','showroom'],['8','{{ __('product.Purchase Price') }}','purchase_price'],['9','{{ __('product.Selling Price') }}','selling_price'],['10','{{ __('product.Stock') }}','stock'],['11','{{ __('common.Created User') }}','created_user']]">
<input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['opening_stock_add_list']) }}">

<input type="hidden" name="products_list_select_option" id="products_list_select_option" value="{{ route('sku_product_select_list_option') }}">
@endsection

@push("scripts")
<script src="{{ Module::asset('core:product_select.js') }}"></script>
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script type="text/javascript">
    function getProductDetails(){
        var sku_id = $('#product_sku_id').val();
        $.post('{{ route('product-details-for-stock') }}', {_token:'{{ csrf_token() }}', id:sku_id}, function(data){
            $('.details').html(data);
        });
    }
</script>
@endpush
