<table class="table Crm_table_active3">
    <thead>
        <tr>
            <th scope="col">{{ __('account.Date') }}</th>
            <th scope="col">{{__('account.ID')}}</th>
            <th scope="col">{{ __('common.Account Name') }}</th>
            <th scope="col">{{ __('account.Description') }}</th>
            <th scope="col">{{ __('account.Debit') }}</th>
            <th scope="col">{{ __('account.Credit') }}</th>
            <th scope="col" class="text-right">{{ __('account.Balance') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
         $currentBalance = $openingBalance + $balance;
$debit_total = 0;
$credit_total = 0;
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
        @foreach ($transactions as $key => $payment)
            @if ($payment->type == "Cr")
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
               <td><a onclick="voucher_detail1({{ $payment->voucherable->id }})">{{ $payment->voucherable->tx_id }}</a></td>
                <td>{{ $payment->account->name }}</td>
                <td>{{ @$payment->voucherable->narration }}</td>
                <td>
                    @if ($payment->type == "Dr")
                        @php($debit_total += $payment->amount)
                        {{ single_price($payment->amount) }}
                        <input type="hidden" name="debit[]" value="{{ $payment->amount }}">
                    @endif
                </td>
                <td>
                    @if ($payment->type == "Cr")
                        @php($credit_total += $payment->amount)
                        {{ single_price($payment->amount) }}
                        <input type="hidden" name="credit[]" value="{{ $payment->amount }}">
                    @endif
                </td>

                <td class="text-right">{{ single_price($currentBalance) }}</td>
            </tr>
        @endforeach

    </tbody>
    <tfoot>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right">{{ __('common.Total') }}</td>
        <td >{{ single_price($debit_total) }}</td>
        <td >{{ single_price($credit_total) }}</td>
        <td class="text-right">{{ single_price($currentBalance) }}</td>
    </tr>
    </tfoot>
</table>
