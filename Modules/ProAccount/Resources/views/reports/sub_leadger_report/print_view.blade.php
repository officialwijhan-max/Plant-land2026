<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('account.partner_account_report') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family:sans-serif,'Almarai';
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
        }
        h1,h2,h3,h4,h5,h6{
            margin: 0;
            color: #000000;
        }
        .table td, .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #000000;
        }

        th p span, td p span{
            color: #000000;
        }
        .table th {
            color: #000000;
            border: 1px solid #000000 !important;
        }
        p {
            font-size: 15px;
            padding: 0px;
            margin: 0px;
            color: #000000;
            /* text-align: justify; */
        }
        h5{
            font-size: 12px;
            font-weight: 500;
        }
        h6{
            font-size: 10px;
            font-weight: 300;
        }
        td.center_padding {
            padding: 0px 20px 0px 20px;
        }
        .bold {
            font-weight: bolder;
        }
        .primary_clr {
            color: #7e7172 !important;
        }
        .mt_40{
            margin-top: 40px;
        }
        .table_style th, .table_style td{
            padding: 20px;
        }

        .text-right{
            text-align: right !important;
        }

        .text-center{
            text-align: center !important;
        }
        .virtical_middle{
            vertical-align: middle !important;
        }
        .logo_img {
            /* max-width: 90px; */
            display: flex;
            justify-content: space-between;
            max-height: 60px;
        }
        .logo_img img{
            width: 50%;
        }
        .company_title {
            position: relative;
            bottom: -20px;
            white-space: nowrap;
        }
        .company_title_arabic {
            position: relative;
            bottom: -17px;
            white-space: nowrap;
        }
        .border_bottom{
            border-bottom: 1px solid #000;
        }
        .nowrap {
            white-space: nowrap !important;
        }
        .mb-0{
            margin-bottom: 0;
        }
        .mb_10{
            margin-bottom: 10px !important;
        }
        .mb_20{
            margin-bottom: 20px !important;
        }
        .border_table thead tr th {
            padding: 5px;
        }
        .border_table tbody tr td {
            border: 1px solid #000000 !important;
            text-align: center;
            padding: 5px;
        }
        .header_table tbody tr td {
            border: 1px solid #000000 !important;
            padding: 5px;
        }
        .no_border_table tbody tr td {
            border: 1px solid #ffffff !important;
            padding: 5px;
        }
        table.table.second_table.mb_20 {
            margin-left: 20px;
        }
        .to_title {
            font-size: 16px;
            font-weight: bold;
            min-width: 15px;
            max-width: 15px;
        }
        td.to_title {
            min-width: 15 px !important;
        }
        span.supplier_name {
            font-size: 16px;
            font-weight: bold;
        }
        td{
            color: #000000;
            font-weight: 500;
            padding: 1px;

        }
        th {
            color: #000000;
            font-weight: bold;
            font-size: 15px;
            padding: 1px;
            text-align: center;
        }
        table{
            width: 100%;
        }
        .text {
            margin-left: 20px;
            margin-right: 20px;
        }
        hr.solid_hr {
            border: 1px solid;
        }
        .gap_div {
            /* position: fixed; */
            bottom: 0;
            width: 100%;
        }
        .rsp {
            margin-left: 10px !important;
            margin-right: 10px !important;
        }
        @page {
            footer: page-footer;
        }
    </style>
</head>
<body>
<div class="invoice_wrapper">
    <div class="invoice_print mb_20">
        <div class="container">
            <div class="invoice_part_iner">
                <table class="table mb_20" >
                    <thead>
                        <tr>
                            <td>
                                <div class="logo_img">
                                    <img src="{{asset(app('general_setting')->logo)}}" alt="">
                                </div>
                            </td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <hr class="solid_hr">
    <div class="text mb_10">
        <h3 class="text-center">{{ trans('account.statement_of_account') }}</h3>
        <hr>
        <table class="table header_table no_border_table mb_20">
            <tbody>
                <tr>
                    <td width="50%">{{trans('account.code')}} : {{ $leadgerAccount->code }}</td>
                    <td width="50%" class="text-right">{{trans('account.printing_date')}} : {{date('m.d.Y')}}</td>
                </tr>
                <tr>
                    <td width="50%">{{trans('account.account_name')}} : {{ $leadgerAccount->name }}</td>
                    <td width="50%" class="text-right">{{trans('account.printing_time')}} : {{date('h:i A')}}</td>
                </tr>
                @isset($dateFrom)
                <tr>
                    <td colspan="2">
                        @isset($dateFrom)
                        {{trans('account.date_from')}} : {{ date('m.d.Y', strtotime(request('dateFrom'))) }}
                        @endisset
                        <span class="rsp"></span>
                        @isset($dateTo)
                        {{trans('account.date_to')}} : {{ date('m.d.Y', strtotime(request('dateTo'))) }}
                        @endisset
                    </td>
                </tr>
                @endisset
            </tbody>
        </table>
    </div>
    <table class="table header_table mb_20">
        @if (in_array($account_type, [1,3]))
            <tbody>
                @foreach ($real_transactions as $leadger_name => $transactions)
                    @php
                        $debit_sum = 0;
                        $credit_sum = 0;
                        $opening_amount = 0;
                        $currentBalance = 0 + $balance;
                    @endphp
                    <tr>
                        <th width="15%">{{ ($leadger_name) ? $leadger_name .' - ('.$transactions->first()->leadger->code.')' : "Opening Balance Initial" }}</th>
                        <th width="15%"></th>
                        <th width="15%"></th>
                        <th>{{ trans('account.debit') }}</th>
                        <th>{{ trans('account.credit') }}</th>
                        <th>{{ trans('account.balance') }}</tthd>
                    </tr>
                    @foreach ($transactions->where('is_opening', 1) as $transaction)
                    @php
                        $amount = $transaction->amount;
                        $opening_amount += $transaction->amount;
                        $currentBalance = $transaction->type == "Cr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
                    @endphp
                    <tr>
                        <td>{{ trans('account.openning_balance') }}</td>
                        <td class="nowrap"></td>
                        <td></td>
                        <td class="nowrap"></td>
                        <td class="nowrap"></td>
                        <td class="nowrap text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                    @endforeach
                    @foreach ($transactions->where('is_opening','!=', 1)->sortBy('voucher.date') as $transaction)
                    @php
                        $amount = $transaction->amount;
                        $currentBalance = $transaction->type == "Cr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
                    @endphp
                    <tr>
                        <td>{{ @$transaction->voucher->GetTypeName()." - ".$transaction->voucher->txn_id }}</td>
                        <td class="nowrap center_padding">{{ showDate($transaction->voucher->date) }}</td>
                        <td>{{ $transaction->narration }}</td>
                        <td class="nowrap center_padding">
                            @if ($transaction->type == "Dr")
                                @php
                                    $credit_sum += $transaction->amount;
                                @endphp
                                {{ number_format($transaction->amount, 2) }}
                            @endif
                        </td>
                        <td class="nowrap center_padding">
                            @if ($transaction->type == "Cr")
                                @php
                                    $debit_sum += $transaction->amount;
                                @endphp
                                {{ number_format($transaction->amount, 2) }}
                            @endif
                        </td>
                        <td class="nowrap center_padding text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ trans('account.total') }}</td>
                        <td class="text-right">{{ ($opening_amount == $credit_sum) ? "" : number_format($credit_sum, 2) }}</td>
                        <td class="text-right">{{ ($opening_amount == $debit_sum) ? "" : number_format($debit_sum, 2) }}</td>
                        <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                @endforeach
            </tbody>
        @else
            <tbody>
                @foreach ($real_transactions as $leadger_name => $transactions)
                    @php
                        $debit_sum = 0;
                        $credit_sum = 0;
                        $opening_amount = 0;
                        $currentBalance = 0 + $balance;
                    @endphp
                    <tr>
                        <th width="15%">{{ ($leadger_name) ? $leadger_name .' - ('.$transactions->first()->leadger->code.')' : "Opening Balance Initial" }}</th>
                        <th width="15%"></th>
                        <th width="15%"></th>
                        <th>{{ trans('account.debit') }}</th>
                        <th>{{ trans('account.credit') }}</th>
                        <th>{{ trans('account.balance') }}</th>
                    </tr>

                    @foreach ($transactions->where('is_opening', 1) as $transaction)
                    @php
                        $opening_amount += $transaction->amount;
                        $amount = $transaction->amount;
                        $currentBalance = $transaction->type == "Dr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
                    @endphp
                    <tr>
                        <td>{{ trans('account.openning_balance') }}</td>
                        <td class="nowrap"></td>
                        <td></td>
                        <td class="nowrap"></td>
                        <td class="nowrap"></td>
                        <td class="nowrap text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                    @endforeach
                    @foreach ($transactions->where('is_opening','!=', 1)->sortBy('voucher.date') as $transaction)
                    @php
                        $amount = $transaction->amount;
                        $currentBalance = $transaction->type == "Dr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
                    @endphp
                    <tr>
                        <td>{{ @$transaction->voucher->GetTypeName()." - ".$transaction->voucher->txn_id }}</td>
                        <td class="nowrap center_padding">{{ showDate($transaction->voucher->date) }}</td>
                        <td>{{ $transaction->narration }}</td>
                        <td class="nowrap center_padding text-right">
                            @if ($transaction->type == "Dr")
                                @php
                                    $debit_sum += $transaction->amount;
                                @endphp
                                {{ number_format($transaction->amount, 2) }}
                            @endif
                        </td>
                        <td class="nowrap center_padding text-right">
                            @if ($transaction->type == "Cr")
                                @php
                                    $credit_sum += $transaction->amount;
                                @endphp
                                {{ number_format($transaction->amount, 2) }}
                            @endif
                        </td>
                        <td class="nowrap center_padding text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ trans('account.total') }}</td>
                        <td class="text-right">{{ ($opening_amount == $debit_sum) ? "" : number_format($debit_sum, 2) }}</td>
                        <td class="text-right">{{ ($opening_amount == $credit_sum) ? "" : number_format($credit_sum, 2) }}</td>
                        <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                @endforeach
            </tbody>
        @endif
    </table>
</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
window.print();
});
</script>
</body>
</html>
