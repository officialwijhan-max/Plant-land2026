<!DOCTYPE html>
<html>
<head>

    <title>{{ trans('account.audit_history') }}</title>

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
            font-family:  DejaVu Sans, sans-serif;
        }
        .invoice_logo {
            margin-bottom: 20px;
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
        }

        .billing_info {
            margin-top: 20px;
        }

        table {
            text-align: left;
            font-family: DejaVu Sans, sans-serif;
        }
        .table thead th {
            border-bottom: 2px solid var(--border_color);
            padding: 5px;
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
            border-top: 2px solid var(--border_color) !important;
        }
        .table tbody td {
            padding: 10px 5px 5px 5px !important;
            font-weight: 500 !important;
        }
        .nowrap {
            white-space: nowrap !important;
        }

        .table td, .table th {
            padding: 0.25rem;
            vertical-align: top;
        }

        td, th {
            color: #828bb2;
            font-size: 10px;
            padding: 0;
            font-weight: 400;
            font-family:  DejaVu Sans, sans-serif;
        }

        th {
            font-weight: 600;
            font-family:  DejaVu Sans, sans-serif;
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
            height:70px;
        }
        .extra_div2 {
            height:15px;
        }
        .a4_width {
            max-width: 1145.28px;
            margin: auto;
        }

        h5 {
            font-size: 13px !important;
            font-weight: 500;
            line-height: 12px;
        }
        .hpb-1{
            padding: 0;
        }
        .text-right{
            text-align: right !important;
        }
        span.space_lr {
            margin-left: 5px;
            margin-right: 5px;
        }
        td.first_td {
            min-width: 150px;
        }
        h5.tbl_title {
            text-align: left;
            margin-top: 5px;
        }
        img.img_size {
            max-height: 95px;
        }
    </style>
</head>
<body>
<div class="container-fluid a4_width">
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
    @php
        $total_journal_transaction_debit = $journal->transactions->where('type', "Dr")->sum('amount');
        $total_journal_transaction_credit = $journal->transactions->where('type', "Cr")->sum('amount');
        $total_debit = 0;
        $total_credit = 0;
    @endphp
    <div class="invoice_info">
        <h5 class="tbl_title text-center">{{ trans('account.journal_details') }} : {{ $journal->GetTypeName()." - ".$journal->txn_id }}</h5>
        <div class="invoice_logo" style="width:50%">
            <table class="table-borderless">
                <tbody>
                    <tr>
                        <td class="first_td">{{ trans('account.txn_id') }}</td>
                        <td><span class="space_lr">:</span>{{ $journal->GetTypeName()." - ".$journal->txn_id }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('account.type') }}:</td>
                        <td><span class="space_lr">:</span>{{ ucwords(str_replace('_',' ',$journal->type)) }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('account.date') }}:</td>
                        <td><span class="space_lr">:</span>{{ date('d-m-Y', strtotime($journal->date)) }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('account.narration') }}:</td>
                        <td><span class="space_lr">:</span>{{ $journal->narration }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('common.Is Approve') }}:</td>
                        <td><span class="space_lr">:</span>{{ $journal->is_approve ? trans('common.Yes') : trans('common.No') }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('common.Approver') }}:</td>
                        <td><span class="space_lr">:</span>{{ $journal->approver->name }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('common.Created At') }}:</td>
                        <td><span class="space_lr">:</span>{{ date('d-m-Y', strtotime($journal->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('account.created_by') }}:</td>
                        <td><span class="space_lr">:</span>{{ @$journal->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('common.Updated At') }}:</td>
                        <td><span class="space_lr">:</span>{{ date('d-m-Y', strtotime($journal->updated_at)) }}</td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('common.Updated By') }}:</td>
                        <td><span class="space_lr">:</span>{{ @$journal->user->email }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="invoice_logo" style="width:50%">
            <table class="table-borderless">
                <tbody>
                    <tr>
                        <td class="first_td">{{ trans('account.is_manually_created') }}:</td>
                        <td><span class="space_lr">:</span>{{ $journal->is_manual_entry == 1 ? trans('common.Yes') : trans('common.No') }}</td>
                    </tr> 
                    <tr>
                        <td class="first_td">{{ trans('account.refer_with') }}</td>
                        <td><span class="space_lr">:</span>
                            {{ ucwords(str_replace('_',' ',$journal->referable->getTable())) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="first_td">{{ trans('account.purpose') }}</td>
                        <td><span class="space_lr">:</span>
                            @if ($journal->sale_or_purchase == "s")
                                {{ trans('account.sales') }}
                            @elseif ($journal->sale_or_purchase == "p")
                                {{ trans('account.purchase') }}
                            @elseif ($journal->sale_or_purchase == "inc")
                                {{ trans('account.income') }}
                            @elseif ($journal->sale_or_purchase == "exp")
                                {{ trans('account.expense') }}
                            @elseif ($journal->sale_or_purchase == "com_pay")
                                {{ trans('account.commision_pay') }}
                            @elseif ($journal->sale_or_purchase == "com_gen")
                                {{ trans('account.commision_generate') }}
                            @elseif ($journal->sale_or_purchase == "payroll_gen")
                                {{ trans('account.payroll_generated') }}
                            @elseif ($journal->sale_or_purchase == "payroll_done")
                                {{ trans('account.payroll_payment') }}
                            @elseif ($journal->sale_or_purchase == "loan_gen")
                                {{ trans('account.loan_generated') }}
                            @elseif ($journal->sale_or_purchase == "loan_pay")
                                {{ trans('account.payment_done_for_loan') }}
                            @elseif ($journal->sale_or_purchase == "opening")
                                {{ trans('account.opening_balance') }}
                            @else
                                {{ trans('account.n/a') }}
                            @endif
                        </td>
                    </tr>
                    @if ($journal->referable->getTable() == "sales" || $journal->referable->getTable() == "purchase_orders")
                        <tr>
                            <td class="first_td">{{ trans('account.invoice_number') }}</td>
                            <td><span class="space_lr">:</span>
                                {{ $journal->referable->invoice_no }}
                            </td>
                        </tr>
                        <tr>
                            <td class="first_td">{{trans('common.Served By')}}</td>
                            <td><span class="space_lr">:</span> {{ ($journal->referable->getTable() == "sales") ? $journal->referable->creator->name : $journal->referable->user->name}}</td>
                        </tr>
                        <tr>
                            <td class="first_td">{{trans('account.approved_by')}}</td>
                            <td><span class="space_lr">:</span> {{ $journal->referable->approver->name }}</td>
                        </tr>
                    @elseif ($journal->referable->getTable() == "purchase_returns")
                        <tr>
                            <td class="first_td">{{ trans('account.invoice_number') }}</td>
                            <td><span class="space_lr">:</span>
                                {{ $journal->referable->purchase_order->invoice_no }}
                            </td>
                        </tr>
                        <tr>
                            <td class="first_td">{{trans('common.Served By')}}</td>
                            <td><span class="space_lr">:</span> {{ $journal->referable->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="first_td">{{trans('account.approved_by')}}</td>
                            <td><span class="space_lr">:</span> {{ $journal->referable->approver->name }}</td>
                        </tr>
                    @elseif ($journal->referable->getTable() == "sale_returns")
                        <tr>
                            <td class="first_td">{{ trans('account.invoice_number') }}</td>
                            <td><span class="space_lr">:</span>
                                {{ $journal->referable->sale->invoice_no }}
                            </td>
                        </tr>
                        <tr>
                            <td class="first_td">{{trans('common.Served By')}}</td>
                            <td><span class="space_lr">:</span> {{ $journal->referable->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="first_td">{{trans('account.approved_by')}}</td>
                            <td><span class="space_lr">:</span> {{ $journal->referable->approver->name }}</td>
                        </tr>
                    @elseif ($journal->referable->getTable() == "payrolls")
                        <tr>
                            <td class="first_td">{{ trans('account.reference_no') }}</td>
                            <td><span class="space_lr">:</span>
                                {{ $journal->referable->payroll_month.' '.$journal->referable->payroll_year }}
                            </td>
                        </tr>
                        <tr>
                            <td class="first_td">{{trans('account.created_by')}}</td>
                            <td><span class="space_lr">:</span> {{ $journal->referable->user->name }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="first_td">{{ trans('account.reference_no') }}</td>
                            <td><span class="space_lr">:</span>
                                {{ trans('account.n/a') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="invoice_info">
        <table class="table table-bordered billing_info">
            <thead>
                <tr>
                    <th scope="col">{{ trans('account.account_name') }}</th>
                    <th scope="col">{{ trans('account.partner_account') }}</th>
                    <th scope="col">{{ trans('account.narration') }}</th>
                    <th scope="col">{{ trans('account.debit') }}</th>
                    <th scope="col">{{ trans('account.credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($journal->transactions->groupBy('leadger_id') as $key => $item)
                    <tr>
                        <td>{{ $item->first()->leadger->name }} ({{ $item->first()->leadger->code }})</td>
                        <td>{{ $item->first()->sub_leadger->name }}</td>
                        <td>{{ $item->first()->narration }}</td>
                        <td class="nowrap text-right">
                            @php
                                $total_debit += $item->where('type', 'Dr')->sum('amount');
                            @endphp
                            {{ single_price($item->where('type', 'Dr')->sum('amount')) }}
                        </td>
                        <td class="nowrap text-right">
                            @php
                                $total_credit += $item->where('type', 'Cr')->sum('amount');
                            @endphp
                            {{ single_price($item->where('type', 'Cr')->sum('amount')) }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3">{{ trans('account.total') }}</td>
                    <td class="text-right">{{ single_price($total_debit) }}</td>
                    <td class="text-right">{{ single_price($total_credit) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
window.print();
});
</script>
</body>
</html>
