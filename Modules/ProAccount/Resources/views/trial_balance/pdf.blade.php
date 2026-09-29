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
    @php
        $total_debit_balance_initial = 0;
        $total_credit_balance_initial = 0;
        $total_debit_balance = 0;
        $total_credit_balance = 0;
        $totalDebit = 0;
        $totalCrebit = 0;
    @endphp
    <div class="invoice_wrapper">
        <!-- invoice print part here -->
        <div class="invoice_print mb_30">
            <div class="container">
                <div class="invoice_part_iner">
                    <table class="table mb_30" >
                        <thead>
                            <tr>
                                <td style="text-align:center">
                                    <div class="logo_img">
                                        <img src="{{URL::to(Settings('invoice_logo'))}}" alt="">
                                    </div>
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <table>
                        <tbody>
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
                    <th scope="col" width="20%"></th>
                    <th scope="col">{{trans('account.initial')}}</th>
                    <th scope="col"></th>
                    <th scope="col">{{ date('Y-m-d') }}</th>
                    <th scope="col"></th>
                    <th scope="col">{{trans('account.total')}}</th>
                    <th scope="col"></th>
                </tr>
                <tr>
                    <th scope="col" width="15%">{{trans('account.account')}}</th>
                    <th scope="col">{{trans('account.debit')}}</th>
                    <th scope="col">{{trans('account.credit')}}</th>
                    <th scope="col">{{trans('account.debit')}}</th>
                    <th scope="col">{{trans('account.credit')}}</th>
                    <th scope="col">{{trans('account.debit')}}</th>
                    <th scope="col">{{trans('account.credit')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leadgers as $key => $leadger)
                    @php
                        $initial_debit = ($leadger->type == 1 || $leadger->type == 3) ? $leadger->BalanceAmountTillDate($start_date, $showroom_id) : 0;
                        $initial_credit = ($leadger->type == 2 || $leadger->type == 4) ? $leadger->BalanceAmountTillDate($start_date, $showroom_id) : 0;
                        $current_debit = ($leadger->type == 1 || $leadger->type == 3) ? $leadger->BalanceAmountBetweenDate($start_date, $end_date, $showroom_id) : 0;
                        $current_credit = ($leadger->type == 2 || $leadger->type == 4) ? $leadger->BalanceAmountBetweenDate($start_date, $end_date, $showroom_id) : 0;
                        $total_debit_balance_initial += $initial_debit;
                        $total_credit_balance_initial += $initial_credit;
                        $total_debit_balance += $current_debit;
                        $total_credit_balance += $current_credit;
                        $totalDebit += $initial_debit + $current_debit;
                        $totalCrebit += $initial_credit + $current_credit;
                    @endphp
                    <tr>
                        <td>{{ $leadger->name }}</td>
                        <td>
                            @if ($leadger->type == 1 || $leadger->type == 3)
                                {{ single_price($initial_debit) }}
                            @endif
                        </td>
                        <td>
                            @if ($leadger->type == 2 || $leadger->type == 4)
                                {{ single_price($initial_credit) }}
                            @endif
                        </td>
                        <td>
                            @if ($leadger->type == 1 || $leadger->type == 3)
                                {{ single_price($current_debit) }}
                            @endif
                        </td>
                        <td>
                            @if ($leadger->type == 2 || $leadger->type == 4)
                                {{ single_price($current_credit) }}
                            @endif
                        </td>
                        <td>{{ single_price($initial_debit + $current_debit) }}</td>
                        <td>{{ single_price($initial_credit + $current_credit) }}</td>
                    </tr>
                @endforeach
                    <tr>
                        <td></td>
                        <td>{{ single_price($total_debit_balance_initial) }}</td>
                        <td>{{ single_price($total_credit_balance_initial) }}</td>
                        <td>{{ single_price($total_debit_balance) }}</td>
                        <td>{{ single_price($total_credit_balance) }}</td>
                        <td>{{ single_price($totalDebit) }}</td>
                        <td>{{ single_price($totalCrebit) }}</td>
                    </tr>
            </tbody>
        </table>
        <htmlpagefooter name="page-footer">
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td>{{Settings('address')}}, {{Settings('country_name')}} <br> Tel : {{Settings('phone')}} FAX : {{Settings('fax')}} <br> Email : {{Settings('email')}}   {{Settings('website')}}</td>
                        <td>
                            <img src="{{URL::to(Settings('qr_code'))}}" style="height: 55px;width: 65px;" alt="">
                        </td>
                        <td style="text-align: right;">{{Settings('address_sl')}}, {{Settings('country_sl')}} <br> : هاتف{{Settings('fax_sl')}} : فاكس    {{Settings('phone_sl')}} <br> {{Settings('email')}}   {{Settings('website')}}</td>
                    </tr>
                </tbody>
            </table>
        </htmlpagefooter>
    </div>
</body>
</html>
