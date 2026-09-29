<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{trans('account.journal')}} Print</title>
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
        .table td , .table th {
            padding: 1px 0;
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
        td, th{
            color: #000000;
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
        table tfoot {
        border-bottom: 1px solid rgb(255, 255, 255);
        }
        @media print{@page {size: landscape}}
    </style>
</head>
<body>
@php
    $total_debit = 0;
    $total_credit = 0;
@endphp
<div class="invoice_wrapper">
    <div class="invoice_print mb_10">
        <div class="container">
            <div class="invoice_part_iner">
                <table class="table mb_20" >
                    <thead>
                        <tr>
                            <td>
                                <div class="logo_img">
                                    <img src="{{asset(app('general_setting')->logo)}}" width="80%" alt="">
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
        <h3 class="text-center">{{ trans('account.voucher_info_details') }}</h3>
    </div>
    <table class="table header_table mb_10">
        <tbody>
            <tr>
                <td>{{trans('account.txn_id')}}</td>
                <td width="25%">{{ $voucher->txn_id }}</td>
                <td>{{trans('account.journal_type')}}</td>
                <td width="25%">{{ $voucher->GetTypeName() }}</td>
                <td>{{trans('account.date')}}</td>
                <td width="25%">{{ showDate($voucher->date) }}</td>
            </tr>
        </tbody>
    </table>
    <table class="table header_table mb_20">
        <tbody>
            <tr>
                <td>{{ trans('account.is_cashflow_journal') }}</td>
                <td> {{ ($voucher->is_cash_flow_journal) ? trans("account.yes") : trans("account.no") }}</td>
            </tr>
            <tr>
                <td>{{ trans('account.created_by') }}</td>
                <td> {{ @$voucher->user->email }}</td>
            </tr>
            <tr>
                <td>{{ trans('account.narration') }}</td>
                <td> {{ $voucher->narration }}</td>
            </tr>
            <tr>
                <td>{{ trans('account.is_invoiced') }}</td>
                <td> {{ ($voucher->is_invoiced) ? trans('account.yes') : trans('account.no') }}</td>
            </tr>
            <tr>
                <td>{{ trans('account.is_advanced') }}</td>
                <td> {{ ($voucher->is_advanced) ? trans('account.yes') : trans('account.no') }}</td>
            </tr>
        </tbody>
    </table>
    @php
        $str = '<?xml version="1.0" encoding="UTF-8"?>';
    @endphp
    <table class="table header_table mb_20">
        <thead>
            <tr>
                <th scope="col">{{ trans('account.account_name') }}</th>
                <th scope="col">{{ trans('account.partner_account') }}</th>
                <th scope="col">{{ trans('account.cash_flow_account') }}</th>
                <th scope="col" width="30%">{{ trans('account.narration') }}</th>
                <th scope="col" width="13%">{{ trans('account.debit') }}</th>
                <th scope="col" width="13%">{{ trans('account.credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($voucher->referable_type == "Modules\Purchase\Entities\CompletePurchase" || $voucher->referable_type == "Modules\Purchase\Entities\ManpowerInvoice")
                @foreach ($voucher->transactions->groupBy('leadger_id') as $item)
                    <tr>
                        <td>{{ $item->first()->leadger->name }} ({{ $item->first()->leadger->code }})</td>
                        <td>{{ $item->first()->sub_leadger->name }}</td>
                        <td>{{ $item->first()->cash_flow_detail->cash_flow_account->name }}</td>
                        <td>{{ $item->first()->narration }}</td>
                        <td class="nowrap">
                            @php
                                $total_debit += $item->where('type', 'Dr')->sum('amount');
                            @endphp
                            {{ single_price($item->where('type', 'Dr')->sum('amount')) }}
                        </td>
                        <td class="nowrap">
                            @php
                                $total_credit += $item->where('type', 'Cr')->sum('amount');
                            @endphp
                            {{ single_price($item->where('type', 'Cr')->sum('amount')) }}
                        </td>
                    </tr>
                @endforeach
            @else
                @foreach ($voucher->transactions as $key => $payment)
                    <tr>
                        <td>{{ $payment->leadger->name }} ({{ $payment->leadger->code }})</td>
                        <td>{{ $payment->sub_leadger->name }}</td>
                        <td>{{ $payment->cash_flow_detail->cash_flow_account->name }}</td>
                        <td>{{ $payment->narration }}</td>
                        <td class="nowrap">
                            @if ($payment->type == "Dr")
                                @php
                                    $total_debit += $payment->amount;
                                @endphp
                                {{ single_price($payment->amount) }}
                            @endif
                        </td>
                        <td class="nowrap">
                            @if ($payment->type == "Cr")
                                @php
                                    $total_credit += $payment->amount;
                                @endphp
                                {{ single_price($payment->amount) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td>{{ trans('account.total') }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="nowrap" id="total_debit"> {{ single_price($total_debit) }}</td>
                <td class="nowrap" id="total_credit"> {{ single_price($total_credit) }}</td>
            </tr>
            <input type="hidden" name="debit_amount_id" id="debit_amount_id" value="1">
            <input type="hidden" name="voucher_approval_url" id="voucher_approval_url" value="{{ route('set_voucher_approval') }}">
        </tbody>
    </table>
    <htmlpagefooter class="gap_div" name="page-footer">
        <table>
            <tbody>
                <tr>
                    <td>
                        <p style="margin-bottom: 5px !important;">{{Settings('address')}}, {{Settings('country_name')}}</p>
                        <p style="margin-bottom: 5px !important;">Tel : {{Settings('phone')}} FAX : {{Settings('fax')}}</p>
                        <p style="margin-bottom: 5px !important;">Email : {{Settings('email')}} , {{Settings('website')}}</p>
                        <p style="margin-bottom: 5px !important;">CR # {{Settings('commercial_registration')}}</p>
                    </td>
                    <td style="text-align: right !important;">
                        <p style="margin-bottom: 5px !important;">{{Settings('address_sl')}}, {{Settings('country_sl')}}</p>
                        <p style="margin-bottom: 5px !important;"> : هاتف{{Settings('fax_sl')}} : فاكس    {{Settings('phone_sl')}}</p>
                        <p style="margin-bottom: 5px !important;">{{Settings('email')}} - {{Settings('website')}}</p>
                        <p style="margin-bottom: 5px !important;"> : س.ت {{Settings('commercial_registration_sl')}}</p>
                    </td>

                </tr>
            </tbody>
        </table>
    </htmlpagefooter>
</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("tfoot").remove();
    window.print();
});
</script>
</body>
</html>
