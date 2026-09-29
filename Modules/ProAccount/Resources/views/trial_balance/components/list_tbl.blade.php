@php
    $total_debit_balance_initial = 0;
    $total_credit_balance_initial = 0;
    $total_debit_balance = 0;
    $total_credit_balance = 0;
    $totalDebit = 0;
    $totalCrebit = 0;
@endphp

<table class="table">
    <thead>
        <tr>
            <th scope="col" colspan="7" class="text-center">{{trans('account.movement_trial_balance')}}@isset($start_date)  {{  trans('account.from').' '. showDate($start_date) }} @endisset @isset($end_date)  {{ trans('account.to') .' '. showDate($end_date) }} @endisset</th>
        </tr>
        <tr>
            <th scope="col" width="20%"></th>
            <th scope="col" colspan="2" class="text-center">{{trans('account.initial')}}</th>
            <th scope="col" colspan="2" class="text-center">{{trans('account.movement_trial_balance')}}</th>
            <th scope="col" colspan="2" class="text-center">{{trans('account.balance')}}</th>
        </tr>
        <tr>
            <th scope="col" width="15%">{{trans('account.account')}}</th>
            <th scope="col" class="text-center">{{trans('account.debit')}}</th>
            <th scope="col" class="text-center">{{trans('account.credit')}}</th>
            <th scope="col" class="text-center">{{trans('account.debit')}}</th>
            <th scope="col" class="text-center">{{trans('account.credit')}}</th>
            <th scope="col" class="text-center">{{trans('account.debit')}}</th>
            <th scope="col" class="text-center">{{trans('account.credit')}}</th>
        </tr>
    </thead>
    <tbody id="datas">
        @foreach ($leadgers as $key => $leadger)
            @php
                $initial_debit = ($leadger->type == 1 || $leadger->type == 3) ? $leadger->BalanceAmountTillDate($start_date, $showroom_id) : 0;
                $initial_credit = ($leadger->type == 2 || $leadger->type == 4) ? $leadger->BalanceAmountTillDate($start_date, $showroom_id) : 0;
                $current_debit = ($leadger->type == 1 || $leadger->type == 3) ? $leadger->BalanceAmountBetweenDate($start_date, $end_date, $showroom_id) : 0;
                $current_credit = ($leadger->type == 2 || $leadger->type == 4) ? $leadger->BalanceAmountBetweenDate($start_date, $end_date, $showroom_id) : 0;
            @endphp
            <tr>
                <td>({{ $leadger->code }}) {{ $leadger->name }}</td>
                <td class="text-center">
                    @if (($leadger->type == 1 || $leadger->type == 3) && $initial_debit >= 0)
                        {{ single_price($initial_debit) }}
                        @php
                            $total_debit_balance_initial += $initial_debit;
                        @endphp
                    @endif
                    @if (($leadger->type == 2 || $leadger->type == 4) && $initial_credit < 0)
                        {{ single_price(abs($initial_credit)) }}
                        @php
                            $total_debit_balance_initial += abs($initial_credit);
                        @endphp
                    @endif
                </td>
                <td class="text-center">
                    @if (($leadger->type == 1 || $leadger->type == 3) && $initial_debit < 0)
                        {{ single_price(abs($initial_debit)) }}
                        @php
                            $total_credit_balance_initial += abs($initial_debit);
                        @endphp
                    @endif
                    @if (($leadger->type == 2 || $leadger->type == 4) && $initial_credit >= 0)
                        {{ single_price($initial_credit) }}
                        @php
                            $total_credit_balance_initial += $initial_credit;
                        @endphp
                    @endif
                </td>
                <td class="text-center">
                    {{ single_price($leadger->DebitBalanceAmountBetweenDate($start_date, $end_date, $showroom_id)) }}
                    @if (($leadger->type == 1 || $leadger->type == 3)  && $current_debit >= 0)
                        @php
                            $total_debit_balance += $current_debit;
                        @endphp
                    @endif
                    @if (($leadger->type == 2 || $leadger->type == 4) && $current_credit < 0)
                        @php
                            $total_debit_balance += abs($current_credit);
                        @endphp
                    @endif
                </td>
                <td class="text-center">
                    {{ single_price($leadger->CreditBalanceAmountBetweenDate($start_date, $end_date, $showroom_id)) }}
                    @if (($leadger->type == 1 || $leadger->type == 3)  && $current_debit < 0)
                        @php
                            $total_credit_balance += abs($current_debit);
                        @endphp
                    @endif
                    @if (($leadger->type == 2 || $leadger->type == 4) && $current_credit >= 0)
                        @php
                            $total_credit_balance += $current_credit;
                        @endphp
                    @endif
                </td>
                @php
                    $sum_debit = $initial_debit + $current_debit;
                    $sum_credit = $initial_credit + $current_credit;
                @endphp
                <td class="text-center">
                    @if (($leadger->type == 1 || $leadger->type == 3)  && $sum_debit >= 0)
                        {{ single_price($sum_debit) }}
                        @php
                            $totalDebit += $sum_debit;
                        @endphp
                    @endif
                    @if (($leadger->type == 2 || $leadger->type == 4) && $sum_credit < 0)
                        {{ single_price(abs($sum_credit)) }}
                        @php
                            $totalDebit += abs($sum_credit);
                        @endphp
                    @endif
                </td>
                <td class="text-center">
                    @if (($leadger->type == 1 || $leadger->type == 3)  && $sum_debit < 0)
                        {{ single_price(abs($sum_debit)) }}
                        @php
                            $totalCrebit += abs($sum_debit);
                        @endphp
                    @endif
                    @if (($leadger->type == 2 || $leadger->type == 4) && $sum_credit >= 0)
                        {{ single_price($sum_credit) }}
                        @php
                            $totalCrebit += $sum_credit;
                        @endphp
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td class="text-center">{{ single_price($total_debit_balance_initial) }}</td>
            <td class="text-center">{{ single_price($total_credit_balance_initial) }}</td>
            <td class="text-center">{{ single_price($total_debit_balance) }}</td>
            <td class="text-center">{{ single_price($total_credit_balance) }}</td>
            <td class="text-center">{{ single_price($totalDebit) }}</td>
            <td class="text-center">{{ single_price($totalCrebit) }}</td>
        </tr>
    </tfoot>
</table>
