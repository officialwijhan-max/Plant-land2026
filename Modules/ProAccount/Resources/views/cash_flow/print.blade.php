<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('account.cash_flow') }} Print</title>
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
        <h3 class="text-center">{{ trans('account.cash_flow') }}</h3>
    </div>
    <table class="table header_table mb_10">
        <thead>
            <tr>
                <th class="text-center" colspan="4">{{trans('account.income')}}</th>
                <th class="text-center" colspan="4">{{trans('account.expense')}}</th>
            </tr>
            @foreach ($datas as $key => $transaction)
                @if ($key == 1)
                    <tr>
                        <th>{{ $transaction->date }}</th>
                        <th>{{ $transaction->leadger_name }}</th>
                        <th>{{ $transaction->code }}</th>
                        <th>{{ $transaction->amount }}</th>
                        <th>{{ $transaction->_date }}</th>
                        <th>{{ $transaction->_leadger_name }}</th>
                        <th>{{ $transaction->_code }}</th>
                        <th>{{ $transaction->_amount }}</th>
                    </tr>
                @endif
            @endforeach
        </thead>
        <tbody>
            @foreach ($datas as $key => $transaction)
                @if ($key > 1)
                    <tr>
                        <td>{{ $transaction->date }}</td>
                        <td>{{ $transaction->leadger_name }}</td>
                        <td>{{ $transaction->code }}</td>
                        <td>{{ $transaction->amount }}</td>
                        <td>{{ $transaction->_date }}</td>
                        <td>{{ $transaction->_leadger_name }}</td>
                        <td>{{ $transaction->_code }}</td>
                        <td>{{ $transaction->_amount }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
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
