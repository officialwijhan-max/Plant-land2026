<!DOCTYPE html>
<html>
<head>

    <title>{{ trans('account.income_statement_report') }} Print</title>

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
        .text-right {
            text-align: right !important;
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
            <img src="{{asset(app('general_setting')->logo)}}" style="max-height: 220px; max width: 500px" alt="">
        </div>
        <div class="invoice_no">
            <h5 class="hpb-1">{{app('general_setting')->company_name}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->phone}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->email}}</h5>
            <h5>{{app('general_setting')->address}}</h5>
            <h5>{{trans("common.Print")}} : {{date('m-d-Y')}}</h5>
        </div>
    </div>
    @if (request('report_type') == null || request('report_type') == "fiscal_year")
        <div class="invoice_info">
            <h6 class="text-center">{{ trans('account.income_statement_report') }}</h6>
            <table class="table table-bordered billing_info m-0">

                <thead>
                    <tr>
                        <th scope="col">{{trans('account.account')}}</th>
                        <th scope="col">{{trans('account.note')}}</th>
                        @foreach ($financial_years as $key => $financial_year)
                            <th scope="col" class="text-right">{{date(Settings("date_format_id"), strtotime($financial_year->start_date)) }} - {{ ($financial_year->end_date != null) ? date(Settings("date_format_id"), strtotime($financial_year->end_date)) : 'Continue' }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class={{ ($direct_income->is_cost_center == 1) ? "mother_leadger" : ""}}>
                            {{ $direct_income->name}}
                        </td>
                        <td class=""></td>
                        @foreach ($financial_years as $k => $financial_year)
                            <td class="text-right"></td>
                        @endforeach
                    </tr>
                    @foreach ($direct_income->childrenCategories as $child_account)
                        @include('proaccount::reports.income_statement_report.component.child_list', ['child_account' => $child_account])
                    @endforeach

                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.total') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            @php
                                $total_direct_income_val[$financial_year->id] = $financial_year->showTotalBalance($direct_income_ids, $showroom_id);
                            @endphp
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($total_direct_income_val[$financial_year->id], $showroom_id)) }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class={{ ($direct_expense->is_cost_center == 1) ? "mother_leadger" : ""}}>
                            {{ $direct_expense->name}}
                        </td>
                        <td class="text-center"></td>
                        @foreach ($financial_years as $k => $financial_year)
                            <td class="text-right"></td>
                        @endforeach
                    </tr>
                    @foreach ($direct_expense->childrenCategories as $child_account)
                        @include('proaccount::reports.income_statement_report.component.child_list', ['child_account' => $child_account])
                    @endforeach

                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.total') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            @php
                                $total_direct_expense_val[$financial_year->id] = $financial_year->showTotalBalance($direct_expense_ids, $showroom_id);
                            @endphp
                            <td class="mother_leadger text-right">{{ single_price($total_direct_expense_val[$financial_year->id]) }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.gross_profit_or_loss') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($total_direct_income_val[$financial_year->id] - $total_direct_expense_val[$financial_year->id]) }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class={{ ($indirect_income->is_cost_center == 1) ? "mother_leadger" : ""}}>
                            {{ $indirect_income->name}}
                        </td>
                        <td class="text-center"></td>
                        @foreach ($financial_years as $k => $financial_year)
                            <td class="text-right"></td>
                        @endforeach
                    </tr>
                    @foreach ($indirect_income->childrenCategories as $child_account)
                        @include('proaccount::reports.income_statement_report.component.child_list', ['child_account' => $child_account])
                    @endforeach

                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.total') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($indirect_income_ids, $showroom_id)) }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class={{ ($indirect_expense->is_cost_center == 1) ? "mother_leadger" : ""}}>
                            {{ $indirect_expense->name}}
                        </td>
                        <td class="text-center"></td>
                        @foreach ($financial_years as $k => $financial_year)
                            <td class="text-right"></td>
                        @endforeach
                    </tr>
                    @foreach ($indirect_expense->childrenCategories as $child_account)
                        @include('proaccount::reports.income_statement_report.component.child_list', ['child_account' => $child_account])
                    @endforeach

                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.total') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($indirect_expense_ids, $showroom_id)) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="mother_leadger" colspan="{{ count($financial_years) + 2 }}"></td>
                    </tr>
                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.profit_and_loss_before_tax') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalProfitLossBeforeTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id)) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.provision_for_tax') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalFinancialYearTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id)) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.net_profit_loss_this_financial_year') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showNetTotalProfitLossFinancialYear($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id)) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        @include('proaccount::reports.income_statement_report.print_view_others')
    @endif

</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        window.print();
    });
</script>
</body>
</html>
