<table class="table" id="leadgerTbl">
    <thead>
        <tr>
            <th scope="col">{{ trans('account.date') }}</th>
            <th scope="col">{{trans('account.txn_id')}}</th>
            <th scope="col">{{ trans('account.narration') }}</th>
            <th scope="col">{{ trans('account.debit') }}</th>
            <th scope="col">{{ trans('account.credit') }}</th>
            <th scope="col" class="text-right">{{ trans('account.balance') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $currentBalance = 0 + $balance;
            $total_dr = 0;
            $total_cr = 0;
        @endphp
        @if ($balance != 0)
            <tr>
                <td colspan="5">{{ trans('account.balance_forwarded') }}</td>
                <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
            </tr>
        @endif
        @php
            $opening_cr_total = $transactions->where('is_opening', 1)->where('type', "Cr")->sum('amount');
            $opening_dr_total = $transactions->where('is_opening', 1)->where('type', "Dr")->sum('amount');
            $current_opening = $opening_cr_total - $opening_dr_total;
            $currentBalance = $currentBalance + ($opening_cr_total - $opening_dr_total);
        @endphp
        @if ($current_opening > 0)
            <tr>
                <td>{{ date(Settings("date_format_id"), strtotime(@$transactions->where('is_opening', 1)->first()->voucher->date)) }}</td>
                <td> <a class="pointer">{{ trans('account.openning_balance') }}</a></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">{{ number_format($current_opening, 2) }}</td>
            </tr>
        @endif
        @foreach ($transactions->where('is_opening','!=', 1)->groupBy('voucher_id') as $transaction)
            @php
            $currentBalance = $transaction->where('type', 'Cr')->count() > 0 ? ($currentBalance + $transaction->where('type', 'Cr')->sum('amount')) :  ($currentBalance - $transaction->where('type', 'Dr')->sum('amount'));
                // $currentBalance = $transaction->type == "Cr" ? ($currentBalance + $transaction->amount) :  ($currentBalance - $transaction->amount);
            @endphp
            <tr>
                <td>
                    {{ date(Settings("date_format_id"), strtotime(@$transaction->first()->voucher->date)) }}
                    @if ($transaction->where('is_reconciled', 1)->first())
                        <span class="badge_1 ml-2">{{ trans('account.C') }}</span>
                    @endif
                </td>
                <td> <a class="voucher_detail pointer" data-id='{{ $transaction->first()->voucher->id }}'>{{  @$transaction->first()->voucher->GetTypeName()." - ".@$transaction->first()->voucher->txn_id }}</a></td>
                <td>{{ $transaction->first()->narration }}</td>
                <td>
                    @if ($transaction->where('type', 'Dr')->count() > 0)
                        @php
                            $total_dr += $transaction->where('type', 'Dr')->sum('amount');
                        @endphp
                        {{ number_format($transaction->where('type', 'Dr')->sum('amount'), 2) }}
                    @endif
                </td>
                <td>
                    @if ($transaction->where('type', 'Cr')->count() > 0)
                        @php
                            $total_cr += $transaction->where('type', 'Cr')->sum('amount');
                        @endphp
                        {{ number_format($transaction->where('type', 'Cr')->sum('amount'), 2) }}
                    @endif
                </td>
                <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3" class="text-right">{{ trans('account.total') }}</td>
            <td>{{ number_format($total_dr, 2) }}</td>
            <td>{{ number_format($total_cr, 2) }}</td>
            <td class="text-right">{{ number_format($currentBalance, 2) }}</td>
        </tr>
    </tbody>
</table>
