
@foreach ($transactions->sort() as $key => $transaction)
    @php
        $amount = $transaction->amount;
    @endphp
    <tr>
        <td>{{ date(Settings("date_format_id"), strtotime(@$transaction->voucher->date)) }}</td>
        <td>{{ @$transaction->voucher->txn_id }}</td>
        <td>{{ $transaction->transaction_data->leadger->name }}</td>
        <td>{{ $transaction->voucher->narration }}</td>
        <td>
            @if ($transaction->type == "Cr")
                {{ single_price( $amount ) }}
            @endif
        </td>
        <td>
            @if ($transaction->type == "Dr")
                {{ single_price( $amount ) }}
            @endif
        </td>
    </tr>
@endforeach
