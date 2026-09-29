@php
    $total_debit = 0;
    $total_credit = 0;
@endphp
<div class="modal fade admin-query" id="Voucher_info_modal">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account.voucher_info_details') }} {{ ($voucher->expense) ? "(Expense Voucher)" : "" }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <table class="table table_modal_upper table-bordered">
                            <tbody>
                                <tr>
                                    <td class="text-left">{{ trans('account.txn_id') }}</td>
                                    <td class="text-left"> {{ $voucher->txn_id }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.is_cashflow_journal') }}</td>
                                    <td class="text-left"> {{ ($voucher->is_cash_flow_journal) ? trans("account.yes") : trans("account.no") }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.date') }}</td>
                                    <td class="text-left"> {{ date('d-m-Y', strtotime($voucher->date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.created_by') }}</td>
                                    <td class="text-left"> {{ @$voucher->user->email }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.narration') }}</td>
                                    <td class="text-left"> {{ $voucher->narration }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.is_invoiced') }}</td>
                                    <td class="text-left"> {{ ($voucher->is_invoiced) ? trans('account.yes') : trans('account.no') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.is_advanced') }}</td>
                                    <td class="text-left"> {{ ($voucher->is_advanced) ? trans('account.yes') : trans('account.no') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('common.Refference') }}</td>
                                    <td class="text-left">
                                        @if ($voucher->referable->getTable() == "sales")
                                            <a href="{{route('sale.show',$voucher->referable->id)}}" target="_blank">{{$voucher->referable->invoice_no}}</a>
                                        @endif
                                        @if ($voucher->referable->getTable() == "purchase_orders")
                                            <a href="{{route('purchase_order.show',$voucher->referable->id)}}" target="_blank">{{$voucher->referable->invoice_no}}</a>
                                        @endif
                                        @if ($voucher->referable_type == null && $voucher->sale_or_purchase != null)
                                            {{ $voucher->ref_no }}
                                        @endif
                                    </td>
                                </tr>
                                <input type="hidden" name="debit_amount_id" id="debit_amount_id" value="1">
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($voucher->type != "misc")
                    {{-- <hr> --}}
                    <div class="row">
                        <div class="col">
                            <table class="table table_modal_upper table-bordered">
                                <tbody>
                                    @foreach ($voucher->transactions->where('type', 'Cr') as $key => $transaction)
                                        <tr>
                                            <td>{{ trans('account.payment_from') }}</td>
                                            <td> {{ $transaction->leadger->name }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach ($voucher->transactions->where('type', 'Dr') as $key => $transaction)
                                        <tr>
                                            <td>{{ trans('account.recieved_by') }}</td>
                                            <td> {{ $transaction->leadger->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                {{-- <hr> --}}
                @if ($voucher->sale_or_purchase != "exp" && $voucher->sale_or_purchase != "inc")
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="">
                                <table class="table table_modal table-bordered mt-5">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="20%">{{ trans('account.account_name') }}</th>
                                            <th scope="col">{{ trans('account.partner_account') }}</th>
                                            @if (Settings('use_cash_flow_in_accounting') == 1)
                                                <th scope="col">{{ trans('account.cash_flow_account') }}</th>
                                            @endif
                                            <th scope="col" width="15%">{{ trans('account.debit') }}</th>
                                            <th scope="col" width="15%">{{ trans('account.credit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($voucher->sale_or_purchase != null || $voucher->referable_type == "Modules\Sales\Entities\CommissionGroup")
                                            @foreach ($voucher->transactions as $item)
                                                <tr>
                                                    <td class="text-wrap"><a class="black-color-text" href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $item->leadger->id]) }}" target="_blank">{{ $item->leadger->name }} ({{ $item->leadger->code }})</a></td>
                                                    <td><a class="black-color-text" href="{{ route('leadger_report.sub_leadger_report_view',['subleagerId' => $item->sub_leadger->id]) }}" target="_blank">{{ $item->sub_leadger->name }}</a></td>
                                                    @if (Settings('use_cash_flow_in_accounting') == 1)
                                                        <td class="text-wrap"><a class="black-color-text" href="{{ route('cash_flow_report.cash_flow_view',['cashflowId' => @$item->cash_flow_detail->cash_flow_account->id]) }}" target="_blank">{{ $item->cash_flow_detail->cash_flow_account->name }}</a></td>
                                                    @endif
                                                    <td class="nowrap">
                                                        @if ($item->type == "Dr")
                                                            @php
                                                                $total_debit += $item->amount;
                                                            @endphp
                                                            {{ single_price($item->amount) }}
                                                        @endif
                                                    </td>
                                                    <td class="nowrap">
                                                        @if ($item->type == "Cr")
                                                            @php
                                                                $total_credit += $item->amount;
                                                            @endphp
                                                            {{ single_price($item->amount) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach ($voucher->transactions->groupBy('leadger_id') as $item)
                                                <tr>
                                                    <td class="text-wrap"><a class="black-color-text" href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $item->first()->leadger->id]) }}" target="_blank">{{ $item->first()->leadger->name }} ({{ $item->first()->leadger->code }})</a></td>
                                                    <td><a class="black-color-text" href="{{ route('leadger_report.sub_leadger_report_view',['subleagerId' => $item->first()->sub_leadger->id]) }}" target="_blank">{{ $item->first()->sub_leadger->name }}</a></td>
                                                    @if (Settings('use_cash_flow_in_accounting') == 1)
                                                        <td class="text-wrap"><a class="black-color-text" href="{{ route('cash_flow_report.cash_flow_view',['cashflowId' => @$item->first()->cash_flow_detail->cash_flow_account->id]) }}" target="_blank">{{ $item->first()->cash_flow_detail->cash_flow_account->name }}</a></td>
                                                    @endif
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
                                        @endif
                                        <tr>
                                            <td>{{ trans('account.total') }}</td>
                                            @if (Settings('use_cash_flow_in_accounting') == 1)
                                                <td></td>
                                            @endif
                                            <td></td>
                                            <td class="nowrap" id="total_debit"> {{ single_price($total_debit) }}</td>
                                            <td class="nowrap" id="total_credit"> {{ single_price($total_credit) }}</td>
                                        </tr>
                                        <input type="hidden" name="debit_amount_id" id="debit_amount_id" value="1">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="">
                                <table class="table table_modal table-bordered mt-5">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="20%">{{ trans('account.account_name') }}</th>
                                            <th scope="col">{{ trans('account.partner_account') }}</th>
                                            <th scope="col" width="30%">{{ ($voucher->sale_or_purchase == "exp") ? trans('account.expense') : trans('account.income') }}</th>
                                            <th scope="col" width="15%">{{ trans('account.debit') }}</th>
                                            <th scope="col" width="15%">{{ trans('account.credit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($voucher->transactions as $item)
                                            <tr>
                                                <td class="text-wrap"><a class="black-color-text" href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $item->leadger->id]) }}" target="_blank">{{ $item->leadger->name }} ({{ $item->leadger->code }})</a></td>
                                                <td class="text-wrap"><a class="black-color-text" href="{{ route('leadger_report.sub_leadger_report_view',['subleagerId' => $item->sub_leadger->id]) }}" target="_blank">{{ $item->sub_leadger->name }}</a></td>
                                                <td class="text-wrap">{{ $item->narration }}</td>
                                                
                                                <td class="nowrap">
                                                    @if ($item->type == "Dr")
                                                        @php
                                                            $total_debit += $item->amount;
                                                        @endphp
                                                        {{ single_price($item->amount) }}
                                                    @endif
                                                </td>
                                                <td class="nowrap">
                                                    @if ($item->type == "Cr")
                                                        @php
                                                            $total_credit += $item->amount;
                                                        @endphp
                                                        {{ single_price($item->amount) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td>{{ trans('account.total') }}</td>
                                            <td></td>
                                            <td></td>
                                            <td class="nowrap" id="total_debit"> {{ single_price($total_debit) }}</td>
                                            <td class="nowrap" id="total_credit"> {{ single_price($total_credit) }}</td>
                                        </tr>
                                        <input type="hidden" name="debit_amount_id" id="debit_amount_id" value="1">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row justify-content-between">
                    <input type="hidden" name="voucher_id" id="voucher_id" value="{{ $voucher->id }}">
                    <div class="col-lg-4">
                        <button type="submit" class="primary-btn radius_30px mb-10 mr-10 fix-gr-bg" data-dismiss="modal"><i class="ti-close"></i>{{ trans('account.close') }}</button>
                    </div>
                    @if ($voucher->is_approve != 1 && strpos(url()->previous(),'approval-list') != false)
                        <div class="col-lg-8 text-right">
                            <button type="submit" class="primary-btn radius_30px mb-10 mr-10 fix-gr-bg pending_btn" data-dismiss="modal"><i class="ti-close"></i>{{ trans('account.cancel') }}</button>
                            <button type="submit" class="primary-btn radius_30px mb-10 mr-10 fix-gr-bg approve_btn" data-dismiss="modal"><i class="ti-check"></i>{{ trans('account.approve') }}</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
