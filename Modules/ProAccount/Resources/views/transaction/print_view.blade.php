<!DOCTYPE html>
<html>
<head>

    <title>{{ trans('account.transaction_history') }} Print</title>

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
        .nowrap{
            white-space: nowrap;
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
    <div class="invoice_info">
        <h6 class="text-center">{{ trans('account.transactions') }} @if ($leadgerAccount) : ({{ $leadgerAccount->code }}) {{$leadgerAccount->name}} @endif</h6>
        <table class="table table-bordered billing_info m-0">
            <thead>
                <tr>
                    <th scope="col" rowspan="2">{{ trans('account.date') }}</th>
                    <th scope="col" rowspan="2">{{ trans('account.leadger_account') }}</th>
                    <th scope="col" rowspan="2">{{trans('account.txn_id')}}</th>
                    <th scope="col" rowspan="2">{{ trans('account.note') }}</th>
                    <th scope="col" colspan="2">{{ trans('account.amount') }}</th>
                </tr>
                <tr>
                    <th scope="col">{{ trans('account.debit') }}</th>
                    <th scope="col">{{ trans('account.credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @if (Settings('accounting_entry_system') != "single_entry")
                    @foreach ($vouchers as $key => $voucher)
                        @foreach ($voucher->transactions as $i => $transaction)
                            @php
                                $total_row = count($voucher->transactions);
                            @endphp
                            <tr>
                                @if ($i == 0)
                                    <td rowspan="{{ $total_row }}">{{ date(Settings("date_format_id"), strtotime($voucher->date)) }}</td>
                                @endif
                                <td>
                                    {{ $transaction->leadger->name }} ({{ $transaction->type }})
                                </td>
                                @if ($i == 0)
                                    <td rowspan="{{ $total_row }}">{{  $voucher->txn_id }}</td>
                                @endif
                                <td>{{ $voucher->narration }}</td>
                                <td class="nowrap">{{ ($transaction->type == "Dr") ? single_price($transaction->amount) : "-" }}</td>
                                <td class="nowrap">{{ ($transaction->type == "Cr") ? single_price($transaction->amount) : "-" }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @else
                    @foreach ($vouchers as $key => $voucher)
                        @foreach ($voucher->transactions as $i => $transaction)
                            @if ($transaction->leadger->acc_type == "cash" || $transaction->leadger->acc_type == "bank")
                                <tr>
                                    <td>{{ date(Settings("date_format_id"), strtotime($voucher->date)) }}</td>
                                    <td>
                                        {{ $transaction->leadger->name }} ({{ $transaction->type }})
                                    </td>
                                    <td>{{ $voucher->txn_id }}</td>
                                    <td>{{ $voucher->narration }}</td>
                                    <td class="nowrap">{{ ($transaction->type == "Dr") ? single_price($transaction->amount) : "-" }}</td>
                                    <td class="nowrap">{{ ($transaction->type == "Cr") ? single_price($transaction->amount) : "-" }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                @endif
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
