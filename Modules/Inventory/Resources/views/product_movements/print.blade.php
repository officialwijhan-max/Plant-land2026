<!DOCTYPE html>
<html>
<head>

    <title>{{ __('inventory.Product Movement') }}</title>

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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'movement_list_product')->first();
        }else {
            $permissions = null;
        }
    @endphp
    <div class="invoice_info">
        <h4 class="text-center">{{ trans('inventory.Product Movement') }}</h4>
        <table class="table table-bordered billing_info m-0">
            <thead>
                @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                        <th scope="col">{{ __('common.ID') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'showroom_or_wareHouse'))
                        <th scope="col">{{ __('sale.Branch / Warehouse') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'purpose'))
                        <th scope="col">{{ __('sale.Purpose') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'product_name'))
                        <th scope="col">{{ __('sale.Product Name') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'quantity'))
                        <th scope="col">{{ __('sale.Quantity') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'date'))
                        <th scope="col">{{ __('sale.Date') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'created_user'))
                        <th scope="col">{{ __('sale.Created User') }}</th>
                        @endif
                    </tr>
                @else
                    <tr>
                        <th scope="col">{{ __('common.ID') }}</th>
                        <th scope="col">{{ __('sale.Branch / Warehouse') }}</th>
                        <th scope="col">{{ __('sale.Purpose') }}</th>
                        <th scope="col">{{ __('sale.Product Name') }}</th>
                        <th scope="col">{{ __('sale.Quantity') }}</th>
                        <th scope="col">{{ __('sale.Date') }}</th>
                        <th scope="col">{{ __('sale.Created User') }}</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach ($items as $key => $item)
                    @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                            <td>{{ $key+1 }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'showroom_or_wareHouse'))
                            <td>{{ (@$item->houseable->saleable->name) ? @$item->houseable->saleable->name : @$item->itemable->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'purpose'))
                            <td>
                                {{ strtoupper(str_replace('_', ' ', $item->type)) }}
                                @if ($item->type == "transferred")
                                    ({{ $item->itemable->name }})
                                @endif
                                @if ($item->type == "Recieve Transfer Item")
                                    ({{ $item->itemable->name }})
                                @endif
                            </td>
                        @endif
                        @if (str_contains($permissions->export_column, 'product_name'))
                            <td>{{ @$item->productSku->product->product_name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'quantity'))
                            <td>{{ $item->in_out }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'date'))
                            <td>{{ $item->date }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'created_user'))
                            <td>{{ userName($item->created_by) }}</td>
                        @endif
                    </tr>
                    @else
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ (@$item->houseable->saleable->name) ? @$item->houseable->saleable->name : @$item->itemable->name }}</td>
                            <td>
                                {{ strtoupper(str_replace('_', ' ', $item->type)) }}
                                @if ($item->type == "transferred")
                                    ({{ $item->itemable->name }})
                                @endif
                                @if ($item->type == "Recieve Transfer Item")
                                    ({{ $item->itemable->name }})
                                @endif
                            </td>
                            <td>{{ @$item->productSku->product->product_name }}</td>
                            <td>{{ $item->in_out }}</td>
                            <td>{{ $item->date }}</td>
                            <td>{{ userName($item->created_by) }}</td>
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
