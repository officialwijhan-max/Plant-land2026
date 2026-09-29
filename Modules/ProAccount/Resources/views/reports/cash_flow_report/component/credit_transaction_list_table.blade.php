<table class="table" id="leadgerTbl">
    <thead>
        <tr>
            <th scope="col">{{ trans('account.date') }}</th>
            <th scope="col">{{trans('account.txn_id')}}</th>
            <th scope="col">{{ trans('account.leadger_account') }}</th>
            <th scope="col">{{ trans('account.note') }}</th>
            <th scope="col">{{ trans('account.debit') }}</th>
            <th scope="col">{{ trans('account.credit') }}</th>
        </tr>
    </thead>
    <tbody id="datas">
        @foreach ($transactions->sort() as $key => $transaction)
            @php
                $amount = $transaction->amount;
            @endphp
            <tr>
                <td>{{ date(Settings("date_format_id"), strtotime(@$transaction->voucher->date)) }}</td>
                <td>{{  @$transaction->voucher->txn_id }}</td>
                <td>{{ $transaction->transaction_data->leadger->name }}</td>
                <td>{{ $transaction->voucher->narration }}</td>
                <td>
                    @if ($transaction->type == "Dr")
                        {{ single_price($amount) }}
                    @endif
                </td>
                <td>
                    @if ($transaction->type == "Cr")
                        {{ single_price($amount) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
