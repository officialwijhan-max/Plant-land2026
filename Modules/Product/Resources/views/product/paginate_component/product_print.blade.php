<!DOCTYPE html>
<html>
<head>

    <title>{{ __('common.Products') }} Print</title>

    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/rtl/bootstrap.min.css"/>
    <style>
        .invoice_heading {
            border-bottom: 1px solid black;
            padding: 20px;
            text-transform: capitalize;
        }
        body{
            font-family: "Poppins", sans-serif;
        }
        .invoice_logo {
            width: 50%;
            float: left;
            text-align: left;
        }

        .invoice_no {
            text-align: right;
            color: #415094;
        }

        .invoice_info {
            padding: 20px;
            width: 100%;
            text-transform: capitalize;
            min-height: 100px;
        }
        table {
            text-align: left;
            font-family: "Poppins", sans-serif;
        }

        td, th {
            color: #828bb2;
            font-size: 13px;
            font-weight: 400;
            font-family: "Poppins", sans-serif;
        }

        th {
            font-weight: 600;
            font-family: "Poppins", sans-serif;
        }
        .margin_120{
            margin-top: 120px;
            font-size: 12px;
        }.margin_12{
            margin-bottom: 120px;
            font-size: 12px;
        }
        .invoice_footer{
            position: absolute;
            left: 0;
            bottom: 180px;
            width: 100%;
        }

        .invoice_info_footer {
            padding: 0px;
            width: 100%;
            left: 0;
            text-transform: capitalize;
            position: inherit;
        }

        p {
            font-size: 10px;
            color: #454545;
            line-height: 16px;
        }
        .extra_div {
            height:100;
        }
        .a4_width {
           max-width: 210mm;
           margin: auto;
        }
        h5 {
            font-size: 13px !important;
            font-weight: 500;
            line-height: 12px;
        }
    </style>
</head>
<body>
<div class="container-fluid ">
    <div class="invoice_heading">
        <div class="invoice_logo">
            <img src="{{asset(app('general_setting')->logo)}}" style="max-height: 110px; max width: 500px" alt="">
        </div>
        <div class="invoice_no">
            <h5 class="hpb-1">{{app('general_setting')->company_name}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->phone}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->email}}</h5>
            <h5>{{app('general_setting')->address}}</h5>
            <h5>{{trans("common.Print")}} : {{date('m-d-Y')}}</h5>
        </div>
    </div>
    @php
        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'product_list')->first();
        }else {
            $permissions = null;
        }
    @endphp
    <div class="invoice_info">
        <h4 class="text-center">{{ __('common.Products') }}</h4>
        <table class="table table-bordered billing_info m-0">
            <thead>
                @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                            <th scope="col">{{__('product.Sl')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'name'))
                            <th scope="col">{{__('common.Name')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'SKU'))
                            @if (app('general_setting')->origin == 1)
                                <th scope="col">{{__('common.Part Number')}}</th>
                            @else
                                <th scope="col">{{__('sale.SKU')}}</th>
                            @endif
                        @endif
                        @if (str_contains($permissions->export_column, 'brand'))
                        <th scope="col">{{__('product.Brand')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'model'))
                        <th scope="col">{{__('product.Model')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'purchase_price'))
                        <th scope="col">{{__('product.Purchase Price')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'selling_price'))
                        <th scope="col">{{__('product.Selling Price')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'min_price'))
                        <th scope="col">{{__('product.Min Price')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'stock'))
                        <th scope="col">{{__('product.Stock')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'supplier'))
                        <th scope="col">{{__('product.Supplier')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'product_type'))
                        <th scope="col">{{__('product.Product Type')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'category'))
                        <th scope="col">{{__('product.Category')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'stock_alert'))
                        <th scope="col">{{__('product.Stock Alert')}}</th>
                        @endif
                    </tr>
                @else
                    <tr>
                        <th scope="col">{{__('product.Sl')}}</th>
                        <th scope="col">{{__('product.Name')}}</th>
                        @if (app('general_setting')->origin == 1)
                            <th scope="col">{{__('common.Part Number')}}</th>
                        @else
                            <th scope="col">{{__('sale.SKU')}}</th>
                        @endif
                        <th scope="col">{{__('product.Brand')}}</th>
                        <th scope="col">{{__('product.Model')}}</th>
                        <th scope="col">{{__('product.Purchase Price')}}</th>
                        <th scope="col">{{__('product.Selling Price')}}</th>
                        <th scope="col">{{__('product.Min Price')}}</th>
                        <th scope="col">{{__('product.Stock')}}</th>
                        <th scope="col">{{__('product.Supplier')}}</th>
                        <th scope="col">{{__('product.Product Type')}}</th>
                        <th scope="col">{{__('product.Category')}}</th>
                        <th scope="col">{{__('product.Stock Alert')}}</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach ($items as $key => $productSkus)
                    @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                            <td>{{ $key+1 }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'name'))
                            <td>{{@$productSkus->product->product_name}}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'SKU'))
                            @if (app('general_setting')->origin == 1)
                                <td>{{ $productSkus->product->origin }}</td>
                            @else
                                <td>{{ $productSkus->sku }}</td>
                            @endif
                        @endif
                        @if (str_contains($permissions->export_column, 'brand'))
                        <td>{{ @$productSkus->product->brand->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'model'))
                        <td>{{ @$productSkus->product->model->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'purchase_price'))
                        <td>{{ single_price($productSkus->purchase_price) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'selling_price'))
                        <td>{{ single_price($productSkus->selling_price) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'min_price'))
                        <td>{{ single_price($productSkus->min_selling_price) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'stock'))
                        <td>{{ ($productSkus->stock()->exists()) ? $productSkus->stock->stock : 0 }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'supplier'))
                        <td>{{ ($productSkus->item()->exists()) ? @$productSkus->item->itemable->supplier->name : 'X' }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'product_type'))
                        <td>{{ (@$productSkus->product->product_type == 'Variable') ? 'Variant' : 'Single' }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'category'))
                        <td>{{ @$productSkus->product->category->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'stock_alert'))
                        <td>{{ $productSkus->alert_quantity.' '.@$productSkus->product->unit_type->name }}</td>
                        @endif
                    </tr>
                    @else
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{@$productSkus->product->product_name}}</td>
                            @if (app('general_setting')->origin == 1)
                                <td>{{ $productSkus->product->origin }}</td>
                            @else
                                <td>{{ $productSkus->sku }}</td>
                            @endif
                            <td>{{ @$productSkus->product->brand->name }}</td>
                            <td>{{ @$productSkus->product->model->name }}</td>
                            <td>{{ single_price($productSkus->purchase_price) }}</td>
                            <td>{{ single_price($productSkus->selling_price) }}</td>
                            <td>{{ single_price($productSkus->min_selling_price) }}</td>
                            <td>{{ ($productSkus->stock()->exists()) ? $productSkus->stock->stock : 0 }}</td>
                            <td>{{ ($productSkus->item()->exists()) ? @$productSkus->item->itemable->supplier->name : 'X' }}</td>
                            <td>{{ (@$productSkus->product->product_type == 'Variable') ? 'Variant' : 'Single' }}</td>
                            <td>{{ @$productSkus->product->category->name }}</td>
                            <td>{{ $productSkus->alert_quantity.' '.@$productSkus->product->unit_type->name }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        window.print();
    });
</script>
</body>
</html>
