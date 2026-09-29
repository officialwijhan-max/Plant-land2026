
    <thead>
        @foreach ($real_transactions as $leadger_name => $transactions)
            @php
                $debit_sum = 0;
                $credit_sum = 0;
                $opening_amount = 0;
                $currentBalance = 0 + $balance;
            @endphp
            <tr class="header_tr">
                <th scope="col" width="15%" class="header_tr">{{ ($leadger_name) ? $leadger_name .' - ('.$transactions->first()->leadger->code.')' : "Opening Balance Initial" }}</th>
                <th scope="col" class="header_tr"></th>
                <th scope="col" class="header_tr"></th>
                <th scope="col" class="header_tr">{{ trans('account.debit') }}</th>
                <th scope="col" class="header_tr">{{ trans('account.credit') }}</th>
                <th scope="col" class="header_tr">{{ trans('account.balance') }}</th>
            </tr>
            @foreach ($transactions->where('is_opening', 1) as $transaction)
            @php
                $amount = $transaction->amount;
                $opening_amount += $transaction->amount;
                $currentBalance = $transaction->type == "Cr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
            @endphp
            <tr>
                <th>{{ trans('account.openning_balance') }}</th>
                <th class="nowrap"></th>
                <th></th>
                <th class="nowrap"></th>
                <th class="nowrap"></th>
                <th class="nowrap">{{ number_format($currentBalance, 2) }}</th>
            </tr>
            @endforeach
            @foreach ($transactions->where('is_opening','!=', 1)->sortBy('voucher.date') as $transaction)
            @php
                $amount = $transaction->amount;
                $currentBalance = $transaction->type == "Cr" ? ($currentBalance - $amount) :  ($currentBalance + $amount);
            @endphp
            <tr>
                <th>{{ @$transaction->voucher->GetTypeName()." - ".$transaction->voucher->txn_id }}</th>
                <th class="nowrap">{{ showDate($transaction->voucher->date) }}</th>
                <th>{{ $transaction->narration }}</th>
                <th class="nowrap">
                    @if ($transaction->type == "Dr")
                        @php
                            $credit_sum += $transaction->amount;
                        @endphp
                        {{ number_format($transaction->amount, 2) }}
                    @endif
                </th>
                <th class="nowrap">
                    @if ($transaction->type == "Cr")
                        @php
                            $debit_sum += $transaction->amount;
                        @endphp
                        {{ number_format($transaction->amount, 2) }}
                    @endif
                </th>
                <th class="nowrap">{{ number_format($currentBalance, 2) }}</th>
            </tr>
            @endforeach
            <tr>
                <th scope="col" width="15%" class="header_tr"></th>
                <th scope="col" class="header_tr"></th>
                <th scope="col" class="header_tr text-right">{{ trans('account.total') }}</th>
                <th scope="col" class="header_tr_2">{{ ($opening_amount == $credit_sum) ? "" : number_format($credit_sum, 2) }}</th>
                <th scope="col" class="header_tr_2">{{ ($opening_amount == $debit_sum) ? "" : number_format($debit_sum, 2) }}</th>
                <th scope="col" class="header_tr_2">{{ number_format($currentBalance, 2) }}</th>
            </tr>
        @endforeach
    </thead>
