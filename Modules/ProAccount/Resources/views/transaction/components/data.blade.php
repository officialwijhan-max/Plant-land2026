
@if (Settings('accounting_entry_system') != "single_entry")
    @foreach ($vouchers as $key => $voucher)
        @php
            $total_row = count($voucher->transactions);
        @endphp
        @foreach ($voucher->transactions as $i => $transaction)
            <tr>
                @if ($i == 0)
                    <td rowspan="{{ $total_row }}">{{ date(Settings("date_format_id"), strtotime($voucher->date)) }}</td>
                @endif
                <td class="text-left pl-3">
                    <a href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $transaction->leadger_id]) }}" class="pointer" target="_blank">{{ $transaction->leadger->name }} ({{ $transaction->type }})</a>
                </td>
                @if ($i == 0)
                    <td rowspan="{{ $total_row }}"><a class="voucher_detail pointer" data-id='{{ $voucher->id }}'>{{  $voucher->txn_id }}</a></td>
                @endif
                <td>{{ $voucher->narration }}</td>
                <td>{{ ($transaction->type == "Dr") ? single_price($transaction->amount) : "-" }}</td>
                <td>{{ ($transaction->type == "Cr") ? single_price($transaction->amount) : "-" }}</td>
                @if ($transaction->type == "Dr")
                    <input type="hidden" name="dr_amount[]" value="{{ $transaction->amount }}">
                @endif
                @if ($transaction->type == "Cr")
                    <input type="hidden" name="cr_amount[]" value="{{ $transaction->amount }}">
                @endif
            </tr>
        @endforeach
    @endforeach
@else
    @foreach ($vouchers as $key => $voucher)
        @php
            $total_row = count($voucher->transactions);
        @endphp
        @foreach ($voucher->transactions as $i => $transaction)
            @if ($transaction->leadger->acc_type == "cash" || $transaction->leadger->acc_type == "bank")
                <tr>
                    <td>{{ date(Settings("date_format_id"), strtotime($voucher->date)) }}</td>
                    <td class="text-left pl-3">
                        <a href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $transaction->leadger_id]) }}" class="pointer" target="_blank">{{ $transaction->leadger->name }} ({{ $transaction->type }})</a>
                    </td>
                    <td><a class="voucher_detail pointer" data-id='{{ $voucher->id }}'>{{  $voucher->txn_id }}</a></td>
                    <td>{{ $voucher->narration }}</td>
                    <td>{{ ($transaction->type == "Dr") ? single_price($transaction->amount) : "-" }}</td>
                    <td>{{ ($transaction->type == "Cr") ? single_price($transaction->amount) : "-" }}</td>
                    @if ($transaction->type == "Dr")
                        <input type="hidden" name="dr_amount[]" value="{{ $transaction->amount }}">
                    @endif
                    @if ($transaction->type == "Cr")
                        <input type="hidden" name="cr_amount[]" value="{{ $transaction->amount }}">
                    @endif
                </tr>
            @endif
        @endforeach
    @endforeach
@endif
