<!DOCTYPE html>
<html>
<head>

    <title>{{ __('common.Invoice') }} Print</title>

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
            <img src="{{asset(app('general_setting')->logo)}}" style="max-height: 110px; max width: 500px" alt="">
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
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">{{ __('account.Date') }}</th>
                    <th scope="col">{{ __('common.Invoice No') }}</th>
                    <th scope="col">{{ __('account.Description') }}</th>
                    <th scope="col">{{ __('account.Debit') }}</th>
                    <th scope="col">{{ __('account.Credit') }}</th>
                    <th scope="col" class="text-right">{{ __('account.Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                        $transactions =  $customer->morph->transactions()->where('is_approve',1)->with(['voucher:id,date,referable_type,referable_id,txn_id,type'])->get(['id','voucher_id','type','amount']);
                    } else {
                        $transactions =  $chartAccount->transactions()->Approved()->get();
                    }
                    $currentBalance = 0 + $customer->opening_balance;
                @endphp
                <tr>
                    <td>{{ __('account.Opening Balance') }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{ single_price($currentBalance) }}</td>
                </tr>
                @if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro")
                    @foreach ($transactions->sortBy('voucher.date') as $key => $transaction)
                        @php
                            $amount = $transaction->amount;
                            $currentBalance = $transaction->type == "Dr" ? ($currentBalance + $amount) :  ($currentBalance - $amount);
                            
                        @endphp
                        <tr>
                            <td class="nowrap">{{ showDate($transaction->voucher->date) }}</th>
                            <td>
                                @if (@$transaction->voucher->referable_id)
                                    <a onclick="getDetails({{ @$transaction->voucher->referable->id }})">{{ @$transaction->voucher->referable->invoice_no }}</a>
                                @else
                                    {{ @$transaction->voucher->GetTypeName()." - ".$transaction->voucher->txn_id }}
                                @endif
                            </td>
                            <td>{{ $transaction->narration }}</th>
                            <td class="nowrap">
                                @if ($transaction->type == "Dr")
                                    {{ single_price($transaction->amount) }}
                                @endif
                            </td>
                            <td class="nowrap">
                                @if ($transaction->type == "Cr")
                                    {{ single_price($transaction->amount) }}
                                @endif
                            </td>
                            <td class="nowrap text-right">{{ single_price($currentBalance) }}</td>
                        </tr>
                    @endforeach
                @else
                    @foreach ($transactions as $key => $payment)
                        @if ($payment->type == "Dr")
                            @php
                                $currentBalance += $payment->amount;
                            @endphp
                        @else
                            @php
                                $currentBalance -= $payment->amount;
                            @endphp
                        @endif
                        <tr>
                            <td>{{ showDate(@$payment->voucherable->date) }}</td>
                            <td>
                                @if (@$payment->voucherable->referable_id)
                                    <a onclick="getDetails({{ @$payment->voucherable->referable->id }})">{{ @$payment->voucherable->referable->invoice_no }}</a>
                                @endif
                            </td>
                            <td>{{ @$payment->voucherable->narration }}</td>
                            <td>
                                @if ($payment->type == "Dr")
                                    {{ single_price($payment->amount) }}
                                    <input type="hidden" name="debit[]" value="{{ $payment->amount }}">
                                @endif
                            </td>
                            <td>
                                @if ($payment->type == "Cr")
                                    {{ single_price($payment->amount) }}
                                    <input type="hidden" name="credit[]" value="{{ $payment->amount }}">
                                @endif
                            </td>
                            <td class="text-right">{{ single_price($currentBalance) }}</td>
                        </tr>
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
