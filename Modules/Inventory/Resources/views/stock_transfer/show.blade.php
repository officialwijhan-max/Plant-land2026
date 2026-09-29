@extends('backEnd.master')
@section('mainContent')
    @push('css')
        <style>
            h1, h2, h3, h4, h5, h6 {
                margin: 0;
            }

            th p span, td p span {
                color: #212E40;
            }

            h5 {
                font-size: 16px;
                font-weight: 500;
                line-height: 23px;
            }

            h6 {
                font-size: 10px;
                font-weight: 300;
            }

            .table_style th, .table_style td {
                padding: 20px;
            }

            p {
                font-size: 10px;
                color: #454545;
                line-height: 16px;
            }

            hr {
                margin: 0 !important;
            }

            table.dataTable tbody td {
                text-align: left;
            }

            @page
            {
                /* this affects the margin in the printer settings */
                margin-top: 1in;
            }
            .a4_width {
               max-width: 793.71px;
               min-height: 1122.52px;
               margin: auto;
            }
            .a4_width_modal {
                max-width: 210mm;
            }
            .modal-content .modal-body {
                border-radius: 15px;
            }
            .extra-margin {
                height: 70px;
            }
            .nowrap{
                white-space: nowrap;
            }
            .hpb-1{
                padding-bottom: 5px;
            }
        </style>
    @endpush

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                @php
                    $setting = app('general_setting');
                    $totalAmount = 0;
                @endphp
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <ul class="d-flex float-right">
                        <li><a class="primary-btn radius_30px fix-gr-bg mr-10" target="_blank"
                               href="{{route('stock-transfer.print_view',$transfer->id)}}">{{__('common.Print')}}</a>
                        </li>
                    </ul>
                </div>

                <div class="col-12 student-details">
                    <ul class="nav nav-tabs tab_column" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#invoice" role="tab"
                               data-toggle="tab">{{__('pos.A4 Size')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade show active a4_width position-relative" id="invoice">
                            <div class="white_box_50px box_shadow_white position-relative a4_width" id="printableArea">
                                <div class="row pb-30 border-bottom">
                                    <div class="col-md-6 col-lg-6">
                                        <img src="{{asset($setting->logo)}}" width="80%" alt="">
                                    </div>
                                    <div class="col-md-6 col-lg-6 text-right">
                                        <h5 class="hpb-1">{{$setting->company_name}}</h5>
                                        <h5 class="hpb-1">{{$setting->phone}}</h5>
                                        <h5 class="hpb-1">{{$setting->email}}</h5>
                                        <h5>{{$setting->address}}</h5>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-6 col-lg-6 col-sm-12">
                                        <table class="table-borderless">
                                            <tr>
                                                <td>{{__('common.Sent From')}}</td>
                                                <td><span class="td_cln">:</span> {{$transfer->sendable->name}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('common.Sent Date')}}</td>
                                                <td><span class="td_cln">:</span> {{showDate($transfer->date)}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('common.Sent By')}}</td>
                                                <td><span class="td_cln">:</span> {{@$transfer->sent_by->name}}</td>
                                            </tr>
                                            @if ($transfer->documents)
                                                <tr>
                                                    <td>{{__('purchase.Download Attachment')}}</td>
                                                    <td>
                                                        <span class="td_cln">:</span><a href="{{$transfer->documents}}">{{ __("leave.attachment") }}</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-12">
                                        <table class="table-borderless mr-0 ml-auto">
                                            <tr>
                                                <td>{{__('common.Recieved At')}}</td>
                                                <td><span class="td_cln">:</span> {{$transfer->receivable->name}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('common.Recieved Date')}}</td>
                                                <td><span class="td_cln">:</span> {{ ($transfer->received_at != null) ? showDate($transfer->received_at) : ''}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('common.Recieved By')}}</td>
                                                <td><span class="td_cln">:</span> {{@$transfer->recieved_by->name}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-10">
                                    <div class="col-12">
                                        <table class="table table-bordered normal_font nowrap">
                                            <thead>
                                                <tr>
                                                    <th>{{__('sale.Product')}}</th>
                                                    <th class="text-center">{{__('product.SKU')}}</th>
                                                    <th class="text-center">{{__('product.Brand')}}</th>
                                                    <th class="text-center">{{__('product.Model')}}</th>
                                                    <th>{{__('product.Price')}}</th>
                                                    <th>{{__('product.QTY')}}</th>
                                                    <th class="text-right">{{__('sale.Total Amount')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($transfer->items as $item)
                                                    @php
                                                        $v_name = [];
                                                        $v_value = [];
                                                        $variantName = null;
                                                        if ($item->productSku->product_variation) {
                                                            foreach (json_decode($item->productSku->product_variation->variant_id) as $key => $value) {
                                                                array_push($v_name , \Modules\Product\Entities\Variant::find($value)->name);
                                                            }
                                                            foreach (json_decode($item->productSku->product_variation->variant_value_id) as $key => $value) {
                                                                array_push($v_value , \Modules\Product\Entities\VariantValues::find($value)->value);
                                                            }

                                                            for ($i=0; $i < count($v_name); $i++) {
                                                                $variantName .= $v_name[$i] . ' : ' . $v_value[$i];
                                                            }
                                                        }
                                                        $subtotal = $item->price * $item->quantity;
                                                        $totalAmount += $subtotal;
                                                    @endphp
                                                    <tr>
                                                        <td>{{@$item->productSku->product->product_name}}
                                                            <br>
                                                            @if ($variantName)
                                                                ({{ $variantName }})
                                                            @endif
                                                        </td>
                                                        <td>{{$item->productable->sku}}</td>
                                                        <td>{{@$item->productSku->product->brand->name}}</td>
                                                        <td>{{@$item->productSku->product->model->name}}</td>
                                                        <td>{{@$item->price}}</td>
                                                        <td class="text-center">{{@$item->quantity}}</td>
                                                        <td class="text-right">{{single_price($subtotal)}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <td colspan="6" style="text-align: right">{{__('common.Total Qty')}}</td>
                                                    <td  class="text-right"><span class="total_quantity">{{number_format($transfer->items->sum('quantity'),2)}}</span></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" style="text-align: right">{{__('sale.SubTotal')}}</td>
                                                    <td class="text-right"><span class="total_amount">{{single_price($totalAmount)}}</span></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-30 mb-60 pb-50">
                                    <div class="col-lg-6 col-md-6 col-sm-6 mt-10 text-justify">
                                        @if ($transfer->notes)
                                            <h3>{{__('common.Note')}}</h3>
                                            <p style="font-size:12px; font-weight:400; color:#828BB2; margin-top:5px">{!! $transfer->notes !!}</p>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 mt-10 text-justify">
                                        @if ($setting->terms_conditions)
                                            <h3>{{__('setting.Terms & Condition')}}</h3>
                                            <p style="font-size:12px; font-weight:400; color:#828BB2; margin-top:5px">{{$setting->terms_conditions}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="extra-margin">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection