<div style="text-align: right;">
    <div class="pdf_btns">
        @if(strpos($_SERVER['REQUEST_URI'], '?') == true)
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=print&purpose=transaction" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
        @else
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=print&purpose=transaction" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
        @endif
    </div>
</div>


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
            @foreach ($transactions->sortBy('voucher.date') as $transaction)
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
