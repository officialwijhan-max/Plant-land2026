<!DOCTYPE html>
<html>
<head>

    <title>Category Print</title>

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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'category_list')->first();
        }else {
            $permissions = null;
        }
    @endphp
    <div class="invoice_info">
        <h4 class="text-center">{{ __('common.Category') }}</h4>
        <table class="table table-bordered billing_info m-0">
            <thead>
                @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'name'))
                            <th scope="col">{{__('common.Name')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'code'))
                            <th scope="col">{{__('common.Code')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'parent'))
                            <th scope="col">{{__('common.Parent')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'description'))
                            <th scope="col">{{__('common.Description')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'status'))
                            <th scope="col">{{__('common.Status')}}</th>
                        @endif
                    </tr>
                @else
                    <tr>
                        <th scope="col">{{__('common.Name')}}</th>
                        <th scope="col">{{__('common.Code')}}</th>
                        <th scope="col">{{__('common.Parent')}}</th>
                        <th scope="col">{{__('common.Description')}}</th>
                        <th scope="col">{{__('common.Status')}}</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach ($items as $key => $category_value)
                    @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'name'))
                            <td>
                                @if ($category_value->level > 0)
                                    @for ($i = 1; $i < $category_value->level; $i++)
                                        -
                                    @endfor
                                ->
                                @endif
                                {{ $category_value->name }}
                            </td>
                        @endif
                        @if (str_contains($permissions->export_column, 'code'))
                            <td>{{ $category_value->code }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'parent'))
                            <td>{{ @$category_value->parentCat->name ?? 'N/A' }} </td>
                        @endif
                        @if (str_contains($permissions->export_column, 'description'))
                            <td>{{ $category_value->description }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'status'))
                            <td>
                                @if($category_value->status == 1)
                                    {{__('common.Active')}}
                                @else
                                    {{__('common.DeActive')}}
                                @endif
                            </td>
                        @endif
                    </tr>
                    @else
                        <tr>
                            <td>
                                @if ($category_value->level > 0)
                                    @for ($i = 1; $i < $category_value->level; $i++)
                                        -
                                    @endfor
                                ->
                                @endif
                                {{ $category_value->name }}
                            </td>
                            <td>{{ $category_value->code }}</td>
                            <td>{{ @$category_value->parentCat->name ?? 'N/A' }} </td>
                            <td>{{ $category_value->description }}</td>
                            <td>
                                @if($category_value->status == 1)
                                    {{__('common.Active')}}
                                @else
                                    {{__('common.DeActive')}}
                                @endif
                            </td>
                        </tr>
                    @endif
                    @if(count($category_value->categories) > 0)
                        @include('product::category.hierarchy_print',['subcategories' => $category_value->categories, 'permissions' => $permissions])
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
