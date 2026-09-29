<div class="modal fade admin-query" id="Voucher_statement_info_modal">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('account.Transaction Details') }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table_modal_upper table-bordered">
                            <tbody>
                                <tr>
                                    <td class="text-left">{{ trans('account.Tx Id') }}</td>
                                    <td class="text-left"> {{ $voucher->tx_id }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.Date') }}</td>
                                    <td class="text-left"> {{ date('d-m-Y', strtotime($voucher->date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.Created By') }}</td>
                                    <td class="text-left"> {{ $voucher->user->email }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.Narration') }}</td>
                                    <td class="text-left"> {{ $voucher->narration }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if ($voucher->document != null)
                    <div class="col-md-12">
                        <table class="table table_modal_upper table-bordered">
                            <tbody>
                                <tr>
                                    <td class="text-left">{{ trans('account.Bank Name') }}</td>
                                    <td class="text-left"> {{ $voucher->document->bank_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.Bank Branch') }}</td>
                                    <td class="text-left"> {{ $voucher->document->bank_branch }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.Cheque No') }}</td>
                                    <td class="text-left"> {{ $voucher->document->cheque_no }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">{{ trans('account.Cheque Date') }}</td>
                                    <td class="text-left"> {{ $voucher->document->cheque_date }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                <hr>
                @php
                    $total_dr = 0;
                    $total_cr = 0;
                @endphp
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table_modal_upper table-bordered">
                            <tbody>
                                <tr>
                                    <th scope="col">{{ __('account.Account Name') }}</th>
                                    <th scope="col">{{ __('account.Debit') }}</th>
                                    <th scope="col">{{ __('account.Credit') }}</th>
                                </tr>
                                @foreach ($voucher->transactions as $key => $payment)
                                    <tr>
                                        <td>{{ $payment->account->name }}</td>
                                        <td>
                                            @if ($payment->type == "Dr")
                                                @php
                                                    $total_dr += $payment->amount;
                                                @endphp
                                                {{ single_price($payment->amount) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment->type == "Cr")
                                                @php
                                                    $total_cr += $payment->amount;
                                                @endphp
                                                {{ single_price($payment->amount) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>{{ __('account.Total') }}</td>
                                    <td id="total_debit">{{ single_price($total_dr) }}</td>
                                    <td id="total_credit">{{ single_price($total_cr) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <input type="hidden" name="voucher_id" id="voucher_id" value="{{ $voucher->id }}">
                    <div class="col-xl-9 mt-2">
                        <button type="submit" class="primary-btn semi_large2 fix-gr-bg" data-dismiss="modal"><i class="ti-close"></i>{{ __('account.Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
