<div class="invoice_info">
    <h6 class="text-center">{{ trans('account.income_statement_report') }}</h6>
    <table class="table table-bordered billing_info m-0">

        <thead>
            <tr>
                <th scope="col">{{trans('account.account')}}</th>
                <th scope="col">{{trans('account.note')}}</th>
                <th scope="col" class="text-right">{{date('Y-m-d', strtotime($dateFrom)) }} - {{ ($dateTo != null) ? date('Y-m-d', strtotime($dateTo)) : 'Continue' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class={{ ($direct_income->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                    {{ $direct_income->name}}
                </td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
            @php
                $total_direct_income = 0;
                $total_indirect_income = 0;
                $total_direct_expense = 0;
                $total_indirect_expense = 0;
            @endphp
            @foreach ($direct_income->childrenCategories as $child_account)
                @include('proaccount::reports.income_statement_report.component.child_list_others', ['child_account' => $child_account])
            @endforeach

            <tr>
                <td class="mother_leadger" colspan="2">
                    {{ trans('account.total') }}
                </td>
                @php
                    foreach ($direct_income->childrenCategories as $key => $leadger_a) {
                        $total_direct_income += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                        foreach ($leadger_a->childrenCategories as $key => $leadger_b) {
                            $total_direct_income += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                            foreach ($leadger_b->childrenCategories as $key => $leadger_c) {
                                $total_direct_income += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                foreach ($leadger_c->childrenCategories as $key => $leadger_d) {
                                    $total_direct_income += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                    foreach ($leadger_d->childrenCategories as $key => $leadger_e) {
                                        $total_direct_income += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        foreach ($leadger_e->childrenCategories as $key => $leadger_f) {
                                            $total_direct_income += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                @endphp
                <td class="mother_leadger text-right">{{ single_price($total_direct_income) }}</td>
            </tr>

            <tr>
                <td class={{ ($direct_expense->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                    {{ $direct_expense->name}}
                </td>
                <td class="text-center"></td>
                <td class="text-right"></td>
            </tr>
            @foreach ($direct_expense->childrenCategories as $child_account)
                @include('proaccount::reports.income_statement_report.component.child_list_others', ['child_account' => $child_account])
            @endforeach

            <tr>
                <td class="mother_leadger" colspan="2">
                    {{ trans('account.total') }}
                </td>
                @php
                    foreach ($direct_expense->childrenCategories as $key => $leadger_a) {
                        $total_direct_expense += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                        foreach ($leadger_a->childrenCategories as $key => $leadger_b) {
                            $total_direct_expense += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                            foreach ($leadger_b->childrenCategories as $key => $leadger_c) {
                                $total_direct_expense += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                foreach ($leadger_c->childrenCategories as $key => $leadger_d) {
                                    $total_direct_expense += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                    foreach ($leadger_d->childrenCategories as $key => $leadger_e) {
                                        $total_direct_expense += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        foreach ($leadger_e->childrenCategories as $key => $leadger_f) {
                                            $total_direct_expense += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                @endphp
                <td class="mother_leadger text-right">{{ single_price($total_direct_expense) }}</td>
            </tr>
                                            
            <tr>
                <td class="mother_leadger" colspan="2">
                    {{ trans('account.gross_profit_or_loss') }}
                </td>
                <td class="mother_leadger text-right">{{ single_price($total_direct_income - $total_direct_expense) }}</td>
            </tr>

            <tr>
                <td class={{ ($indirect_income->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                    {{ $indirect_income->name}}
                </td>
                <td class="text-center"></td>
                <td class="text-right"></td>
            </tr>
            @foreach ($indirect_income->childrenCategories as $child_account)
                @include('proaccount::reports.income_statement_report.component.child_list_others', ['child_account' => $child_account])
            @endforeach

            <tr>
                <td class="mother_leadger" colspan="2">
                    {{ trans('account.total') }}
                </td>
                @php
                    foreach ($indirect_income->childrenCategories as $key => $leadger_a) {
                        $total_indirect_income += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                        foreach ($leadger_a->childrenCategories as $key => $leadger_b) {
                            $total_indirect_income += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                            foreach ($leadger_b->childrenCategories as $key => $leadger_c) {
                                $total_indirect_income += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                foreach ($leadger_c->childrenCategories as $key => $leadger_d) {
                                    $total_indirect_income += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                    foreach ($leadger_d->childrenCategories as $key => $leadger_e) {
                                        $total_indirect_income += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        foreach ($leadger_e->childrenCategories as $key => $leadger_f) {
                                            $total_indirect_income += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                @endphp
                <td class="mother_leadger text-right">{{ single_price($total_indirect_income) }}</td>
            </tr>

            <tr>
                <td class={{ ($indirect_expense->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                    {{ $indirect_expense->name}}
                </td>
                <td class="text-center"></td>
                <td class="text-right"></td>
            </tr>
            @foreach ($indirect_expense->childrenCategories as $child_account)
                @include('proaccount::reports.income_statement_report.component.child_list_others', ['child_account' => $child_account])
            @endforeach

            <tr>
                <td class="mother_leadger" colspan="2">
                    {{ trans('account.total') }}
                </td>
                @php
                    foreach ($indirect_expense->childrenCategories as $key => $leadger_a) {
                        $total_indirect_expense += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                        foreach ($leadger_a->childrenCategories as $key => $leadger_b) {
                            $total_indirect_expense += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                            foreach ($leadger_b->childrenCategories as $key => $leadger_c) {
                                $total_indirect_expense += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                foreach ($leadger_c->childrenCategories as $key => $leadger_d) {
                                    $total_indirect_expense += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                    foreach ($leadger_d->childrenCategories as $key => $leadger_e) {
                                        $total_indirect_expense += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        foreach ($leadger_e->childrenCategories as $key => $leadger_f) {
                                            $total_indirect_expense += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                @endphp
                <td class="mother_leadger text-right">{{ single_price($total_indirect_expense) }}</td>
            </tr>
            <tr>
                <td class="mother_leadger" colspan="2"></td>
            </tr>
            <tr>
                <td class="mother_leadger" colspan="2">
                    {{ trans('account.profit_and_loss_before_tax') }}
                </td>
                @php
                    $total_tax = 0;
                    $profit_and_loss_before_tax = $total_direct_income + $total_indirect_income - $total_direct_expense - $total_indirect_expense;
                    $total_tax = $profit_and_loss_before_tax * Settings('company_tax') / 100;
                    $net_profit_loss_this_financial_year = $profit_and_loss_before_tax - $total_tax;
                @endphp
                <td class="mother_leadger text-right">{{ single_price($profit_and_loss_before_tax) }}</td>
            </tr>
            <tr>
                <td class="mother_leadger" colspan="2">
                    {{ trans('account.provision_for_tax') }}
                </td>
                <td class="mother_leadger text-right">{{ single_price($total_tax) }}</td>
            </tr>
            <tr>
                <td class="mother_leadger" colspan="2">
                    {{ trans('account.net_profit_loss_this_financial_year') }}
                </td>
                <td class="mother_leadger text-right">{{ single_price($net_profit_loss_this_financial_year) }}</td>
            </tr>
        </tbody>
    </table>
</div>