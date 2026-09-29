<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('account.statement_of_account') }} Print</title>
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
        .badge_1.ml-2 {
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 1px solid;
            display: inline-block;
            border-radius: 50%;
            padding: 2px 5px;
            margin-left: 15px;
            white-space: nowrap;
            line-height: 1.2;
            text-transform: capitalize;
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
        <thead>
            <tr>
                <th scope="col" width="15%">{{ trans('account.date') }}</th>
                <th scope="col">{{trans('account.txn_id')}}</th>
                <th scope="col" width="20%">{{ trans('account.note') }}</th>
                <th scope="col">{{ trans('account.debit') }}</th>
                <th scope="col">{{ trans('account.credit') }}</th>
                <th scope="col">{{ trans('account.balance') }}</th>
            </tr>
        </thead>
        @if ($account_type == 1 || $account_type == 3)
            <tbody>
                @php
                    $currentBalance = 0 + $balance;
                    $total_dr = 0;
                    $total_cr = 0;
                @endphp
                @if ($balance != 0)
                    <tr>
                        <td colspan="5">{{ trans('account.balance_forwarded') }}</td>
                        <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                @endif
                @php
                    $opening_cr_total = $transactions->where('is_opening', 1)->where('type', "Cr")->sum('amount');
                    $opening_dr_total = $transactions->where('is_opening', 1)->where('type', "Dr")->sum('amount');
                    $current_opening = $opening_dr_total - $opening_cr_total;
                    $currentBalance = $currentBalance + ($opening_dr_total - $opening_cr_total);
                @endphp
                @if ($current_opening > 0)
                    <tr>
                        <td>{{ date(Settings("date_format_id"), strtotime(@$transactions->where('is_opening', 1)->first()->voucher->date)) }}</td>
                        <td> <a class="pointer">{{ trans('account.openning_balance') }}</a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ number_format($current_opening, 2) }}</td>
                    </tr>
                @endif

                @foreach ($transactions->where('is_opening','!=', 1)->groupBy('voucher_id') as $transaction)
                    @php
                        $currentBalance = $transaction->where('type', 'Dr')->count() > 0 ? ($currentBalance + $transaction->where('type', 'Dr')->sum('amount')) :  ($currentBalance - $transaction->where('type', 'Cr')->sum('amount'));
                    @endphp
                    <tr>
                        <td>
                            {{ date(Settings("date_format_id"), strtotime(@$transaction->first()->voucher->date)) }}
                            @if ($transaction->where('is_reconciled', 1)->first())
                                <span class="badge_1 ml-2">{{ trans('account.C') }}</span>
                            @endif
                        </td>
                        <td> <a class="voucher_detail pointer" data-id='{{ $transaction->first()->voucher->id }}'>{{  @$transaction->first()->voucher->GetTypeName()." - ".@$transaction->first()->voucher->txn_id }}</a></td>
                        <td>{{ $transaction->first()->narration }}</td>
                        <td>
                            @if ($transaction->where('type', 'Dr')->count() > 0)
                                @php
                                    $total_dr += $transaction->where('type', 'Dr')->sum('amount');
                                @endphp
                                {{ number_format($transaction->where('type', 'Dr')->sum('amount'), 2) }}
                            @endif
                        </td>
                        <td>
                            @if ($transaction->where('type', 'Cr')->count() > 0)
                                @php
                                    $total_cr += $transaction->where('type', 'Cr')->sum('amount');
                                @endphp
                                {{ number_format($transaction->where('type', 'Cr')->sum('amount'), 2) }}
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right">{{ trans('account.total') }}</td>
                    <td>{{ number_format($total_dr, 2) }}</td>
                    <td>{{ number_format($total_cr, 2) }}</td>
                    <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                </tr>
            </tbody>
        @else
            <tbody>
                @php
                    $currentBalance = 0 + $balance;
                    $total_dr = 0;
                    $total_cr = 0;
                @endphp
                    @if ($balance != 0)
                        <tr>
                            <td colspan="5">{{ trans('account.balance_forwarded') }}</td>
                            <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                        </tr>
                    @endif
                    @php
                        $opening_cr_total = $transactions->where('is_opening', 1)->where('type', "Cr")->sum('amount');
                        $opening_dr_total = $transactions->where('is_opening', 1)->where('type', "Dr")->sum('amount');
                        $current_opening = $opening_cr_total - $opening_dr_total;
                        $currentBalance = $currentBalance + ($opening_cr_total - $opening_dr_total);
                    @endphp
                    @if ($current_opening > 0)
                        <tr>
                            <td>{{ date(Settings("date_format_id"), strtotime(@$transactions->where('is_opening', 1)->first()->voucher->date)) }}</td>
                            <td> <a class="pointer">{{ trans('account.openning_balance') }}</a></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">{{ number_format($current_opening, 2) }}</td>
                        </tr>
                    @endif

                    @foreach ($transactions->where('is_opening','!=', 1)->groupBy('voucher_id') as $transaction)
                        @php
                            $currentBalance = $transaction->where('type', 'Cr')->count() > 0 ? ($currentBalance + $transaction->where('type', 'Cr')->sum('amount')) :  ($currentBalance - $transaction->where('type', 'Dr')->sum('amount'));
                            // $currentBalance = $transaction->type == "Cr" ? ($currentBalance + $transaction->amount) :  ($currentBalance - $transaction->amount);
                        @endphp
                        <tr>
                            <td>
                                {{ date(Settings("date_format_id"), strtotime(@$transaction->first()->voucher->date)) }}
                                @if ($transaction->where('is_reconciled', 1)->first())
                                    <span class="badge_1 ml-2">{{ trans('account.C') }}</span>
                                @endif
                            </td>
                            <td> <a class="voucher_detail pointer" data-id='{{ $transaction->first()->voucher->id }}'>{{  @$transaction->first()->voucher->GetTypeName()." - ".@$transaction->first()->voucher->txn_id }}</a></td>
                            <td>{{ $transaction->first()->narration }}</td>
                            <td>
                                @if ($transaction->where('type', 'Dr')->count() > 0)
                                    @php
                                        $total_dr += $transaction->where('type', 'Dr')->sum('amount');
                                    @endphp
                                    {{ number_format($transaction->where('type', 'Dr')->sum('amount'), 2) }}
                                @endif
                            </td>
                            <td>
                                @if ($transaction->where('type', 'Cr')->count() > 0)
                                    @php
                                        $total_cr += $transaction->where('type', 'Cr')->sum('amount');
                                    @endphp
                                    {{ number_format($transaction->where('type', 'Cr')->sum('amount'), 2) }}
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                        </tr>
                    @endforeach
                <tr>
                    <td colspan="3" class="text-right">{{ trans('account.total') }}</td>
                    <td>{{ number_format($total_dr, 2) }}</td>
                    <td>{{ number_format($total_cr, 2) }}</td>
                    <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                </tr>
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
