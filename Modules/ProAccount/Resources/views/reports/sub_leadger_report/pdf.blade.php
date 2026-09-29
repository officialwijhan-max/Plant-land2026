<!DOCTYPE html>
<html>
<head>
    <title>Report Print</title>
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
            width: 50%;

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
                                <td style="text-align:center; width: 100%;" ><h4>{{ trans('account.partner_ledger_reports') }} : ({{ $leadgerAccount->code }}) {{$leadgerAccount->name}}</h4></td>
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
            @if (in_array($account_type, [1,3]))
                <tbody>
                    @foreach ($real_transactions as $leadger_name => $transactions)
                        @php
                            $debit_sum = 0;
                            $credit_sum = 0;
                            $opening_amount = 0;
                            $currentBalance = 0 + $balance;
                        @endphp
                        <tr class="header_tr">
                            <th scope="col" width="15%" class="header_tr">{{ ($leadger_name) ? $leadger_name .' - ('.$transactions->first()->leadger->code.')' : "Opening Balance Initial" }}</th>
                            <th scope="col" class="header_tr"></th>
                            <th scope="col" class="header_tr"></th>
                            <th scope="col" class="header_tr">{{ trans('account.debit') }}</th>
                            <th scope="col" class="header_tr">{{ trans('account.credit') }}</th>
                            <th scope="col" class="header_tr">{{ trans('account.balance') }}</th>
                        </tr>
                        @foreach ($transactions->where('is_opening', 1) as $transaction)
                        @php
                            $amount = $transaction->amount;
                            $opening_amount += $transaction->amount;
                            $currentBalance = $transaction->type == "Cr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
                        @endphp
                        <tr>
                            <th>{{ trans('account.openning_balance') }}</th>
                            <th class="nowrap"></th>
                            <th></th>
                            <th class="nowrap"></th>
                            <th class="nowrap"></th>
                            <th class="nowrap">{{ number_format($currentBalance, 2) }}</th>
                        </tr>
                        @endforeach
                        @foreach ($transactions->where('is_opening','!=', 1)->sortBy('voucher.date') as $transaction)
                        @php
                            $amount = $transaction->amount;
                            $currentBalance = $transaction->type == "Cr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
                        @endphp
                        <tr>
                            <th>{{ @$transaction->voucher->GetTypeName()." - ".$transaction->voucher->txn_id }}</th>
                            <th class="nowrap">{{ showDate($transaction->voucher->date) }}</th>
                            <th>{{ $transaction->narration }}</th>
                            <th class="nowrap">
                                @if ($transaction->type == "Dr")
                                    @php
                                        $credit_sum += $transaction->amount;
                                    @endphp
                                    {{ number_format($transaction->amount, 2) }}
                                @endif
                            </th>
                            <th class="nowrap">
                                @if ($transaction->type == "Cr")
                                    @php
                                        $debit_sum += $transaction->amount;
                                    @endphp
                                    {{ number_format($transaction->amount, 2) }}
                                @endif
                            </th>
                            <th class="nowrap">{{ number_format($currentBalance, 2) }}</th>
                        </tr>
                        @endforeach
                        <tr>
                            <th scope="col" width="15%" class="header_tr"></th>
                            <th scope="col" class="header_tr"></th>
                            <th scope="col" class="header_tr text-right">{{ trans('account.total') }}</th>
                            <th scope="col" class="header_tr_2">{{ ($opening_amount == $credit_sum) ? "" : number_format($credit_sum, 2) }}</th>
                            <th scope="col" class="header_tr_2">{{ ($opening_amount == $debit_sum) ? "" : number_format($debit_sum, 2) }}</th>
                            <th scope="col" class="header_tr_2">{{ number_format($currentBalance, 2) }}</th>
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
                        @endphp
                        @php
                        $currentBalance = 0 + $balance;
                    @endphp
                        <tr class="header_tr">
                            <th scope="col" width="15%" class="header_tr">{{ ($leadger_name) ? $leadger_name .' - ('.$transactions->first()->leadger->code.')' : "Opening Balance Initial" }}</th>
                            <th scope="col" class="header_tr"></th>
                            <th scope="col" class="header_tr"></th>
                            <th scope="col" class="header_tr">{{ trans('account.debit') }}</th>
                            <th scope="col" class="header_tr">{{ trans('account.credit') }}</th>
                            <th scope="col" class="header_tr">{{ trans('account.balance') }}</th>
                        </tr>

                        @foreach ($transactions->where('is_opening', 1) as $transaction)
                        @php
                            $opening_amount += $transaction->amount;
                            $amount = $transaction->amount;
                            $currentBalance = $transaction->type == "Dr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
                        @endphp
                        <tr>
                            <th>{{ trans('account.openning_balance') }}</th>
                            <th class="nowrap"></th>
                            <th></th>
                            <th class="nowrap"></th>
                            <th class="nowrap"></th>
                            <th class="nowrap">{{ number_format($currentBalance, 2) }}</th>
                        </tr>
                        @endforeach
                        @foreach ($transactions->where('is_opening','!=', 1)->sortBy('voucher.date') as $transaction)
                        @php
                            $amount = $transaction->amount;
                            $currentBalance = $transaction->type == "Dr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
                        @endphp
                        <tr>
                            <th>{{ @$transaction->voucher->GetTypeName()." - ".$transaction->voucher->txn_id }}</th>
                            <th class="nowrap">{{ showDate($transaction->voucher->date) }}</th>
                            <th>{{ $transaction->narration }}</th>
                            <th class="nowrap">
                                @if ($transaction->type == "Dr")
                                    @php
                                        $debit_sum += $transaction->amount;
                                    @endphp
                                    {{ number_format($transaction->amount, 2) }}
                                @endif
                            </th>
                            <th class="nowrap">
                                @if ($transaction->type == "Cr")
                                    @php
                                        $credit_sum += $transaction->amount;
                                    @endphp
                                    {{ number_format($transaction->amount, 2) }}
                                @endif
                            </th>
                            <th class="nowrap">{{ number_format($currentBalance, 2) }}</th>
                        </tr>
                        @endforeach
                        <tr>
                            <th scope="col" width="15%" class="header_tr"></th>
                            <th scope="col" class="header_tr"></th>
                            <th scope="col" class="header_tr text-right">{{ trans('account.total') }}</th>
                            <th scope="col" class="header_tr_2">{{ ($opening_amount == $debit_sum) ? "" : number_format($debit_sum, 2) }}</th>
                            <th scope="col" class="header_tr_2">{{ ($opening_amount == $credit_sum) ? "" : number_format($credit_sum, 2) }}</th>
                            <th scope="col" class="header_tr_2">{{ number_format($currentBalance, 2) }}</th>
                        </tr>
                    @endforeach
                </tbody>
            @endif
        </table>
    </div>
</body>
</html>
