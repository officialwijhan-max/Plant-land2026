<table class="table">
    <thead>
        <tr>
            <th scope="col">{{ __('account.Date') }}</th>
            <th scope="col">{{ __('account.Reference No.') }}</th>
            <th scope="col">{{ __('account.Description') }}</th>
            <th scope="col">{{ __('account.Debit') }}</th>
            <th scope="col">{{ __('account.Credit') }}</th>
            <th scope="col">{{ __('Expense') }}</th>
            <th scope="col" class="text-right">{{ __('P & L') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $currentBalance = 0 + $balance + $opening_balance;
            $openingBalance = $balance + $opening_balance;
            $totalDebit = 0;
            $totalCredit = 0;
            $totalExpense = 0;
        @endphp
        <tr>
            <td>{{ __('account.Opening Balance') }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">{{ single_price($currentBalance) }}</td>
        </tr>
        @foreach ($transactions->sort() as $key => $payment)
            @if ($payment->type != "Dr")
                @php
                    $currentBalance -= $payment->amount;
                @endphp
            @else
                @php
                    $currentBalance += $payment->amount;
                @endphp
            @endif
            <tr>
                <td>{{ showDate(@$payment->voucherable->date) }}</td>
                <td>
                  <a onclick="voucher_detail({{ $payment->voucherable->id }})">{{ (@$payment->voucherable->referable->invoice_no) ? @$payment->voucherable->referable->invoice_no : @$payment->voucherable->tx_id }}</a>
                </td>
                <td>{{ @$payment->voucherable->narration }}</td>
                <td>
                    @if ($payment->type == "Dr")
                        {{ single_price($payment->amount) }}
                        @php
                            $totalDebit += $payment->amount;
                        @endphp
                        <input type="hidden" name="debit[]" value="{{ $payment->amount }}">
                    @endif
                </td>
                <td>
                    @if ($payment->type == "Cr" && $payment->voucherable->payment_type != 'contra_voucher')
                        {{ single_price($payment->amount) }}
                        @php
                            $totalCredit += $payment->amount;
                        @endphp
                        <input type="hidden" name="credit[]" value="{{ $payment->amount }}">
                    @endif
                </td>
                <td>
                    @if ($payment->type == "Cr" && $payment->voucherable->payment_type == 'contra_voucher')
                        {{ single_price($payment->amount) }}
                        @php
                            $totalExpense += $payment->amount;
                        @endphp
                        <input type="hidden" name="credit[]" value="{{ $payment->amount }}">
                    @endif
                </td>
                <td class="text-right">{{ single_price($currentBalance) }}</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>
                @php
                    $totalDue = $totalDebit + $openingBalance;
                    $profitLoss = $totalDue - $totalExpense;
                @endphp
                {{ single_price($totalDue) }}
            </td>
            <td>{{ single_price($totalCredit) }}</td>
            <td>{{ single_price($totalExpense) }}</td>
            <td class="text-right">{{ single_price($profitLoss) }}</td>
        </tr>
    </tbody>
</table>
