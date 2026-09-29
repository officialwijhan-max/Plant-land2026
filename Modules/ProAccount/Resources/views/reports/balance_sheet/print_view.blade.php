<!DOCTYPE html>
<html>
<head>

    <title>{{ trans('account.balance_sheet_report') }} Print</title>

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
    @php
        $difference = [];
    @endphp
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
            <h6 class="text-center">{{ trans('account.balance_sheet_report') }}</h6>
            <table class="table table-bordered billing_info m-0">
                <thead>
                    <tr>
                        <th scope="col">{{trans('account.account')}}</th>
                        <th scope="col">{{trans('account.note')}}</th>
                        @foreach ($financial_years as $key => $financial_year)
                            <th scope="col" class="text-right">{{ ($financial_year->end_date != null) ? date(Settings("date_format_id"), strtotime($financial_year->end_date)) : 'Continue' }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="mother_leadger red_input text-center" colspan="{{ count($financial_years) + 2 }}">
                            {{ trans('account.assets') }}
                        </td>
                    </tr>
                    @foreach ($assets as $key => $asset)
                        @if ($asset->parent_id != 0)
                            <tr>
                                <td class={{ ($asset->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                                    {{ $asset->name}}
                                </td>
                                <td class="text-center"></td>
                                @foreach ($financial_years as $k => $financial_year)
                                    <td class="text-center"></td>
                                @endforeach
                            </tr>
                        @endif
                        @foreach ($asset->childrenCategories as $child_account)
                            @include('proaccount::reports.balance_sheet.component.child_list', ['child_account' => $child_account])
                        @endforeach
                    @endforeach

                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.total') .' '. trans('account.assets')}}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            @php
                                $running_total_asset = $financial_year->showTotalBalance($assets_ids, $showroom_id);
                                $running_total_liability = $financial_year->showTotalBalance($liabilities_ids, $showroom_id);
                                $difference[$financial_year->id] = $running_total_asset - $running_total_liability;
                            @endphp
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($assets_ids, $showroom_id)) }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="mother_leadger red_input text-center" colspan="{{ count($financial_years) + 2 }}">
                            {{ trans('account.liabilities_and_equity') }}
                        </td>
                    </tr>

                    @foreach ($liabilities as $key => $liability)
                        @if ($liability->parent_id != 0)
                            <tr>
                                <td class={{ ($liability->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                                    {{ $liability->name}}
                                </td>
                                <td class="text-center"></td>
                                @foreach ($financial_years as $k => $financial_year)
                                    <td class="text-right"></td>
                                @endforeach
                            </tr>
                        @endif
                        @foreach ($liability->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $child_account)
                            @include('proaccount::reports.balance_sheet.component.child_list', ['child_account' => $child_account])
                        @endforeach
                    @endforeach

                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.liabilities_and_equity') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($liabilities_ids, $showroom_id)) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.approximate_profit_loss') .'('.trans('account.current_period').')'}}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($difference[$financial_year->id]) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.total') .' '. trans('account.liabilities_and_equity') }}
                        </td>
                        @foreach ($financial_years as $j => $financial_year)
                            <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($liabilities_ids, $showroom_id) + $difference[$financial_year->id]) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        @php
            $total_direct_income = 0;
            $total_direct_expense = 0;
        @endphp
        <div class="invoice_info">
            <h6 class="text-center">{{ trans('account.balance_sheet_report') }}</h6>
            <table class="table table-bordered billing_info m-0">
                <thead>
                    <tr>
                        <th scope="col">{{trans('account.account')}}</th>
                        <th scope="col">{{trans('account.note')}}</th>
                        <th scope="col" class="text-right">{{date('Y-m-d', strtotime($dateFrom)) }} - {{ ($dateTo != null) ? date('Y-m-d', strtotime($dateTo)) : 'Continue' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="mother_leadger red_input" colspan="3">
                            {{ trans('account.assets') }}
                        </td>
                    </tr>
                    @foreach ($assets as $key => $asset)
                        @if ($asset->parent_id != 0)
                            <tr>
                                <td class={{ ($asset->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                                    {{ $asset->name}}
                                </td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                            </tr>
                        @endif

                        @php
                            foreach ($asset->childrenCategories as $key => $leadger_a) {
                                $total_direct_income += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                foreach ($leadger_a->childrenCategories as $key => $leadger_b) {
                                    $total_direct_income += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                    foreach ($leadger_b->childrenCategories as $key => $leadger_c) {
                                        $total_direct_income += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        foreach ($leadger_c->childrenCategories as $key => $leadger_d) {
                                            $total_direct_income += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                            foreach ($leadger_d->childrenCategories as $key => $leadger_e) {
                                                $total_direct_income += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                foreach ($leadger_e->childrenCategories as $key => $leadger_f) {
                                                    $total_direct_income += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        @foreach ($asset->childrenCategories as $child_account)
                            @include('proaccount::reports.balance_sheet.component.child_list_others', ['child_account' => $child_account])
                        @endforeach
                    @endforeach
                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.total') .' '. trans('account.assets')}}
                        </td>
                        <td class="mother_leadger text-right">{{ single_price($total_direct_income) }}</td>
                    </tr>

                    <tr>
                        <td class="mother_leadger red_input" colspan="{{ count($financial_years) + 2 }}">
                            {{ trans('account.liabilities_and_equity') }}
                        </td>
                    </tr>
                    @foreach ($liabilities as $key => $liability)
                        @if ($liability->parent_id != 0)
                            <tr>
                                <td class={{ ($liability->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                                    {{ $liability->name}}
                                </td>
                                <td class="text-center"></td>
                                <td class="text-right"></td>
                            </tr>
                        @endif

                        @php
                            foreach ($liability->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_a) {
                                $total_direct_expense += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                foreach ($leadger_a->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_b) {
                                    $total_direct_expense += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                    foreach ($leadger_b->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_c) {
                                        $total_direct_expense += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        foreach ($leadger_c->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_d) {
                                            $total_direct_expense += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                            foreach ($leadger_d->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_e) {
                                                $total_direct_expense += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                foreach ($leadger_e->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_f) {
                                                    $total_direct_expense += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp

                        @foreach ($liability->childrenCategories->whereNotIn('id', Settings('retail_earning_leadger')) as $child_account)
                            @include('proaccount::reports.balance_sheet.component.child_list_others', ['child_account' => $child_account])
                        @endforeach
                    @endforeach

                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.liabilities_and_equity') }}
                        </td>
                        <td class="mother_leadger text-right">{{ single_price($total_direct_expense) }}</td>
                    </tr>
                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.approximate_profit_loss') .'('.trans('account.current_period').')'}}
                        </td>
                        <td class="mother_leadger text-right" colspan="{{ count($financial_years) }}">{{ single_price($total_direct_income - $total_direct_expense) }}</td>
                    </tr>
                    <tr>
                        <td class="mother_leadger" colspan="2">
                            {{ trans('account.total') .' '. trans('account.liabilities_and_equity') }}
                        </td>
                        <td class="mother_leadger text-right">{{ single_price($total_direct_expense + ($total_direct_income - $total_direct_expense)) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
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
