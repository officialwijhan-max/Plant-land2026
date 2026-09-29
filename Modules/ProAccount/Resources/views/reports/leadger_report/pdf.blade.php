<!DOCTYPE html>
<html>
<head>
    <title>Ledger Report PDF</title>
    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    <style>
        body {
            font-family:sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
        }
        h1,h2,h3,h4,h5,h6{
            margin: 0;
            color: #101010;
        }
        .invoice_wrapper{
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 20px;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }
        .border_none{
            border: 0px solid transparent;
            border-top: 0px solid transparent !important;
        }
        .invoice_part_iner{
            background-color: #fff;
        }

        .table_border thead{
            background-color: #F6F8FA;
        }
        .table td, .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #101010;
        }
        .table td , .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #101010;
        }
        .table_border tr{
            border-bottom: 1px solid #101010 !important;
        }
        th p span, td p span{
            color: #212E40;
        }
        .table th {
            color: #101010;
            border: 1px solid #101010 !important;
        }
        p{
            font-size: 14px;
            color: #101010;
        }
        h5{
            font-size: 12px;
            font-weight: 500;
        }
        h6{
            font-size: 10px;
            font-weight: 300;
        }
        .mt_40{
            margin-top: 40px;
        }
        .table_style th, .table_style td{
            padding: 20px;
        }
        .invoice_info_table td{
            font-size: 10px;
            padding: 0px;
        }

        .text_right{
            text-align: right;
        }
        .virtical_middle{
            vertical-align: middle !important;
        }
        .logo_img {
            max-width: 120px;
        }
        .logo_img img{
            width: 100%;

        }
        .border_bottom{
            border-bottom: 1px solid #000;
        }
        .line_grid{
            display: grid;
            grid-template-columns: 110px auto;
            grid-gap: 10px;
        }
        .line_grid span{
            display: flex;
            justify-content: space-between;
        }

        .line_grid2{
            display: grid;
            grid-template-columns:  auto 110px;
            grid-gap: 10px;
        }
        .line_grid2 span{
            display: flex;
            justify-content: space-between;
        }
        p{
            margin: 0;
        }
        .font_18 {
            font-size: 18px;
        }
        .mb-0{
            margin-bottom: 0;
        }
        .mb_30{
            margin-bottom: 30px !important;
        }
        .border_table{}
        .border_table thead tr th {
            padding: 5px;
        }
        .border_table tbody tr td {
            border: 1px solid #101010 !important;
            text-align: center;
            padding: 5px;
        }
        td, th{
            color: #101010;
            font-weight: 500;
            padding: 5px;

        }
        table{
            width: 100%;
        }
        .text_underline{
            text-decoration: underline;
        }
        @page {
            footer: page-footer;
        }
    </style>
</head>
<body>
    <div class="invoice_wrapper">
        <!-- invoice print part here -->
        <div class="invoice_print mb_30">
            <div class="container">
                <div class="invoice_part_iner">
                    <table class="table mb_30" >
                        <thead>
                            <tr>
                                <td>
                                    <div class="logo_img">
                                        <img src="{{asset(app('general_setting')->logo)}}" width="50%" alt="">
                                    </div>
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <table>
                        <tbody>
                            <tr>
                                <td style="text-align:center; width: 100%;" ><h4>{{ trans('account.ledger_reports') }} : ({{ $leadgerAccount->code }}) {{$leadgerAccount->name}}</h4></td>
                            </tr>
                            <tr>
                                <td style="text-align:center; width: 100%;" ><h4 class="text_underline">{{trans('account.print')}} : {{date('m-d-Y')}}</h4></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- invoice print part end -->
        <table class="table border_table mb_30" >
            <thead>
                <tr>
                    <th scope="col">{{ trans('account.date') }}</th>
                    <th scope="col">{{trans('account.txn_id')}}</th>
                    <th scope="col">{{ trans('account.note') }}</th>
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
                @foreach ($transactions->where('is_opening', 1) as $transaction)
                    @php
                        // $amount = $transaction->sum('amount');
                        $currentBalance = $transaction->type == "Dr" ? ($currentBalance + $transaction->amount) :  ($currentBalance - $transaction->amount);
                    @endphp
                    <tr>
                        <td></td>
                        <td> <a class="voucher_detail pointer" data-id='{{ $transaction->voucher->id }}'>{{ trans('account.openning_balance') }}</a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                @endforeach
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
                @foreach ($transactions->where('is_opening', 1) as $transaction)
                    @php
                        // $amount = $transaction->sum('amount');
                        $currentBalance = $transaction->type == "Cr" ? ($currentBalance + $transaction->amount) :  ($currentBalance - $transaction->amount);
                    @endphp
                    <tr>
                        <td><a class="voucher_detail pointer" data-id='{{ $transaction->voucher->id }}'>{{ trans('account.openning_balance') }}</a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                @endforeach
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
</body>
</html>
