@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans("sales::sale.audit_history"))
@push('css')
    <style>
    .table tbody td {
        padding: 10px 5px 5px 5px !important;
        font-weight: 500 !important;
    }
    .table_details tbody td {
        padding: 0px 1px 1px 1px !important;
        font-weight: 500 !important;
    }
    .nowrap {
        white-space: nowrap;
    }
    span.space_lr {
        margin-left: 5px;
        margin-right: 5px;
    }
    td.first_td {
        min-width: 150px;
    }
    .table-responsive {
        min-height: 0px !important;
        background-color: #fff;
    }
    </style>
@endpush
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{ trans('account.journal_transaction') }} </h3>
                            <ul class="d-flex">
                                <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" href="{{route('journal.transaction_detail_print',$journal->id)}}"><i class="ti-printer"></i>{{ trans('common.Print') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="row mb-3">
                            @php
                                $total_journal_transaction_debit = $journal->transactions->where('type', "Dr")->sum('amount');
                                $total_journal_transaction_credit = $journal->transactions->where('type', "Cr")->sum('amount');
                                $total_debit = 0;
                                $total_credit = 0;
                            @endphp
                            <div class="col-md-6">
                                <h5 class="mb-0 mr-30">{{ trans('account.journal_details') }} : {{ $journal->GetTypeName()." - ".$journal->txn_id }}</h5>
                                <table class="table_details mt-3">
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
                            <div class="col-md-6">
                                <h5 class="mb-0 mr-30">{{ trans('account.reference_details') }}</h5>
                                <table class="table_details mt-3">
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
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered mt-3">
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
                                            @foreach ($journal->transactions as $key => $item)
                                                <tr>
                                                    <td>{{ $item->leadger->name }} ({{ $item->leadger->code }})</td>
                                                    <td>{{ $item->sub_leadger->name }}</td>
                                                    <td>{{ $item->narration }}</td>
                                                    <td class="nowrap text-right">
                                                        @php
                                                            $total_debit += ($item->type == "Dr") ? $item->amount : 0;
                                                        @endphp
                                                        {{ ($item->type == "Dr") ? single_price($item->amount) : '' }}
                                                    </td>
                                                    <td class="nowrap text-right">
                                                        @php
                                                            $total_credit +=  ($item->type == "Cr") ? $item->amount : 0;
                                                        @endphp
                                                        {{ ($item->type == "Cr") ? single_price($item->amount) : '' }}
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push("scripts")

@endpush
