<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('account.trial_balance') }} Print</title>
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
            color: #101010;
        }
        .table td, .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #101010;
        }
        .table td , .table th {
            padding: 1px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #101010;
        }

        th p span, td p span{
            color: #212E40;
        }
        .table th {
            color: #101010;
            border: 1px solid #101010 !important;
        }
        p {
            font-size: 15px;
            padding: 0px;
            margin: 0px;
            color: #101010;
            text-align: justify;
        }
        h5{
            font-size: 12px;
            font-weight: 500;
        }
        h6{
            font-size: 10px;
            font-weight: 300;
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
        }=
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
            border: 1px solid #101010 !important;
            text-align: center;
            padding: 5px;
        }
        .header_table tbody tr td {
            border: 1px solid #101010 !important;
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
        td, th{
            color: #101010;
            font-weight: 500;
            padding: 1px;

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
    <div class="invoice_print mb_20">
        <div class="container">
            <div class="invoice_part_iner">
                <table class="table mb_20" >
                    <thead>
                        <tr>
                            <td>
                                <div class="logo_img">
                                    <img src="{{asset(app('general_setting')->logo)}}" alt="">
                                    @if (Settings("hide_show_company_name") == "show")
                                        <div class="title">
                                            <h3 class="company_title_arabic">{{ Settings('company_name_sl') }}</h3>
                                            <h3 class="company_title">{{ Settings('company_name') }}</h3>
                                        </div>
                                    @endif
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
        <h3 class="text-center">{{ trans('account.trial_balance') }}</h3>
    </div>
    <table class="table header_table mb_20">
        <thead>
            <tr>
                <th scope="col" colspan="7" class="text-center">{{trans('account.movement_trial_balance')}}@isset($start_date)  {{  trans('account.from').' '. showDate($start_date) }} @endisset @isset($end_date)  {{ trans('account.to') .' '. showDate($end_date) }} @endisset</th>
            </tr>
            <tr>
                <th scope="col" width="20%"></th>
                <th scope="col" colspan="2" class="text-center">{{trans('account.initial')}}</th>
                <th scope="col" colspan="2" class="text-center">{{trans('account.movement_trial_balance')}}</th>
                <th scope="col" colspan="2" class="text-center">{{trans('account.balance')}}</th>
            </tr>
            <tr>
                <th scope="col" width="15%">{{trans('account.account')}}</th>
                <th scope="col" class="text-center">{{trans('account.debit')}}</th>
                <th scope="col" class="text-center">{{trans('account.credit')}}</th>
                <th scope="col" class="text-center">{{trans('account.debit')}}</th>
                <th scope="col" class="text-center">{{trans('account.credit')}}</th>
                <th scope="col" class="text-center">{{trans('account.debit')}}</th>
                <th scope="col" class="text-center">{{trans('account.credit')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leadgers as $key => $leadger)
                @php
                    $initial_debit = ($leadger->type == 1 || $leadger->type == 3) ? $leadger->BalanceAmountTillDate($start_date, $showroom_id) : 0;
                    $initial_credit = ($leadger->type == 2 || $leadger->type == 4) ? $leadger->BalanceAmountTillDate($start_date, $showroom_id) : 0;
                    $current_debit = ($leadger->type == 1 || $leadger->type == 3) ? $leadger->BalanceAmountBetweenDate($start_date, $end_date, $showroom_id) : 0;
                    $current_credit = ($leadger->type == 2 || $leadger->type == 4) ? $leadger->BalanceAmountBetweenDate($start_date, $end_date, $showroom_id) : 0;
                @endphp
                <tr>
                    <td>({{ $leadger->code }}) {{ $leadger->name }}</td>
                    <td class="text-center">
                        @if (($leadger->type == 1 || $leadger->type == 3) && $initial_debit >= 0)
                            {{ single_price($initial_debit) }}
                            @php
                                $total_debit_balance_initial += $initial_debit;
                            @endphp
                        @endif
                        @if (($leadger->type == 2 || $leadger->type == 4) && $initial_credit < 0)
                            {{ single_price(abs($initial_credit)) }}
                            @php
                                $total_debit_balance_initial += abs($initial_credit);
                            @endphp
                        @endif
                    </td>
                    <td class="text-center">
                        @if (($leadger->type == 1 || $leadger->type == 3) && $initial_debit < 0)
                            {{ single_price(abs($initial_debit)) }}
                            @php
                                $total_credit_balance_initial += abs($initial_debit);
                            @endphp
                        @endif
                        @if (($leadger->type == 2 || $leadger->type == 4) && $initial_credit >= 0)
                            {{ single_price($initial_credit) }}
                            @php
                                $total_credit_balance_initial += $initial_credit;
                            @endphp
                        @endif
                    </td>
                    <td class="text-center">
                        {{ single_price($leadger->DebitBalanceAmountBetweenDate($start_date, $end_date, $showroom_id)) }}
                        @if (($leadger->type == 1 || $leadger->type == 3)  && $current_debit >= 0)
                            @php
                                $total_debit_balance += $current_debit;
                            @endphp
                        @endif
                        @if (($leadger->type == 2 || $leadger->type == 4) && $current_credit < 0)
                            @php
                                $total_debit_balance += abs($current_credit);
                            @endphp
                        @endif
                    </td>
                    <td class="text-center">
                        {{ single_price($leadger->CreditBalanceAmountBetweenDate($start_date, $end_date, $showroom_id)) }}
                        @if (($leadger->type == 1 || $leadger->type == 3)  && $current_debit < 0)
                            @php
                                $total_credit_balance += abs($current_debit);
                            @endphp
                        @endif
                        @if (($leadger->type == 2 || $leadger->type == 4) && $current_credit >= 0)
                            @php
                                $total_credit_balance += $current_credit;
                            @endphp
                        @endif
                    </td>
                    @php
                        $sum_debit = $initial_debit + $current_debit;
                        $sum_credit = $initial_credit + $current_credit;
                    @endphp
                    <td class="text-center">
                        @if (($leadger->type == 1 || $leadger->type == 3)  && $sum_debit >= 0)
                            {{ single_price($sum_debit) }}
                            @php
                                $totalDebit += $sum_debit;
                            @endphp
                        @endif
                        @if (($leadger->type == 2 || $leadger->type == 4) && $sum_credit < 0)
                            {{ single_price(abs($sum_credit)) }}
                            @php
                                $totalDebit += abs($sum_credit);
                            @endphp
                        @endif
                    </td>
                    <td class="text-center">
                        @if (($leadger->type == 1 || $leadger->type == 3)  && $sum_debit < 0)
                            {{ single_price(abs($sum_debit)) }}
                            @php
                                $totalCrebit += abs($sum_debit);
                            @endphp
                        @endif
                        @if (($leadger->type == 2 || $leadger->type == 4) && $sum_credit >= 0)
                            {{ single_price($sum_credit) }}
                            @php
                                $totalCrebit += $sum_credit;
                            @endphp
                        @endif
                    </td>
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
    @php
        $str = '<?xml version="1.0" encoding="UTF-8"?>';
    @endphp
    <htmlpagefooter name="page-footer">
        <hr>
        <table>
            <tbody>
                <tr>
                    <td>{{Settings('address')}}, {{Settings('country_name')}} <br> Tel : {{Settings('phone')}} FAX : {{Settings('fax')}} <br> Email : {{Settings('email')}}   {{Settings('website')}} <br> CR # {{Settings('commercial_registration')}}</td>
                    {{-- <td class="center_padding">
                        {!! str_replace($str,"",QrCode::size(65)->generate((Settings('website')) ? Settings('website') : "yourwebsite_address")) !!}
                    </td> --}}
                    <td style="text-align: right;">{{Settings('address_sl')}}, {{Settings('country_sl')}} <br> : هاتف{{Settings('fax_sl')}} : فاكس    {{Settings('phone_sl')}} <br> {{Settings('email')}}   {{Settings('website')}} <br> : س.ت {{Settings('commercial_registration_sl')}}</td>
                </tr>
            </tbody>
        </table>
    </htmlpagefooter>
</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.2.1.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
window.print();
});
</script>
</body>
</html>
