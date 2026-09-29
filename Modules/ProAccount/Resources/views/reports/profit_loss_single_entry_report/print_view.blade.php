<!DOCTYPE html>
<html>
<head>

    <title>{{ trans('account.profit_loss_report') }} Print</title>

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
    @php
        $total_profit_cash = 0;
        $total_profit_bank = 0;
    @endphp
    <div class="invoice_info">
        <h6 class="text-center">{{ trans('account.profit_loss') }}</h6>
        <table class="table table-bordered billing_info m-0">
            <thead>
                <tr>
                    <th scope="col">{{trans('account.date')}}</th>
                    <th scope="col">{{trans('account.account')}}</th>
                    <th scope="col" class="text-right">{{trans('account.income')}}</th>
                    <th scope="col" class="text-right">{{trans('account.expense')}}</th>
                    <th scope="col" class="text-right">{{trans('account.profit')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="mother_leadger nowrap" colspan="5">{{ trans('account.cash_income') }}</td>
                </tr>
                @foreach ($cash_data as $key => $cash_info)
                @php
                    $total_profit_cash += $cash_info['dr_amount'];
                @endphp
                    <tr>
                        <td class="mother_leadger nowrap">{{ $cash_info['date'] }}</td>
                        <td>{{ $cash_info['leadger_name'] }}</td>
                        <td class="text-right">{{ single_price($cash_info['dr_amount']) }}</td>
                        <td class="text-right">{{ single_price($cash_info['cr_amount']) }}</td>
                        <td class="text-right">{{ single_price($cash_info['dr_amount'] - $cash_info['cr_amount']) }}</td>
                    </tr>
                    @foreach ($cash_info['details'] as $detail)
                    <tr class="data_{{ $key }}_tr d-none">
                        <td class="nowrap">{{ $detail->date }}</td>
                        <td>{{ $detail->leadger->name }}</td>
                        <td class="text-right">{{ ($detail->type == "Dr") ? single_price($detail->amount) : '' }}</td>
                        <td class="text-right">{{ ($detail->type == "Cr") ? single_price($detail->amount) : '' }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td class="nowrap" colspan="4">{{ trans('account.total') }}</td>
                    <td class="text-right">{{ single_price($total_profit_cash) }}</td>
                </tr>

                <tr>
                    <td class="mother_leadger nowrap" colspan="4">{{ trans('account.bank_income') }}</td>
                </tr>
                @foreach ($bank_data as $m => $bank_info)
                @php
                    $total_profit_bank += $bank_info['dr_amount'];
                @endphp
                    <tr>
                        <td class="mother_leadger nowrap">{{ $bank_info['date'] }}</td>
                        <td>{{ $bank_info['leadger_name'] }}</td>
                        <td class="text-right">{{ single_price($bank_info['dr_amount']) }}</td>
                        <td class="text-right">{{ single_price($bank_info['cr_amount']) }}</td>
                        <td class="text-right">{{ single_price($bank_info['dr_amount'] - $bank_info['cr_amount']) }}</td>
                    </tr>
                    @foreach ($bank_info['details'] as $b_detail)
                    <tr class="bank_data_{{ $m }}_tr d-none">
                        <td class="nowrap">{{ $b_detail->date }}</td>
                        <td>{{ $b_detail->leadger->name }}</td>
                        <td class="text-right">{{ ($b_detail->type == "Dr") ? single_price($b_detail->amount) : '' }}</td>
                        <td class="text-right">{{ ($b_detail->type == "Cr") ? single_price($b_detail->amount) : '' }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td class="nowrap" colspan="4">{{ trans('account.total') }}</td>
                    <td class="text-right">{{ single_price($total_profit_bank) }}</td>
                </tr>
                <tr>
                    <td class="mother_leadger nowrap" colspan="4">{{ trans('account.grand_total') }}</td>
                    <td class="text-right">{{ single_price($total_profit_bank + $total_profit_cash) }}</td>
                </tr>
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
