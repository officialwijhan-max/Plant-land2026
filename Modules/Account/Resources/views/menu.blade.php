@php

    $account = false;
    $transfer = false;

    if(request()->is('account/*'))
    {
        $account = true;
    }
    if(request()->is('transfer/*'))
    {
        $transfer = true;
    }

@endphp
@if(permissionCheck('accounts'))
    <li class="{{ $account ?'mm-active' : '' }}">
        <a href="javascript:" class="has-arrow" aria-expanded="false">
            <div class="nav_icon_small">
                <span class="fa fa-bar-chart"></span>
            </div>
            <div class="nav_title">
                <span>{{ __('account.Accounts') }}</span>
            </div>
        </a>
        <ul>
            @if (permissionCheck('expenses.create'))
                <li>
                    <a href="{{ route('expenses.create') }}"
                       class="{{ spn_active_link(['expenses.create']) }}">{{ __('inventory.Add Expense') }}</a>
                </li>
            @endif
            @if (permissionCheck('expenses.index'))
                <li>
                    <a href="{{ route('expenses.index') }}"
                       class="{{ spn_active_link(['expenses.index']) }}">{{ __('inventory.Expense Lists') }}</a>
                </li>
            @endif

            @if (permissionCheck('income.create'))
                <li>
                    <a href="{{ route('income.create') }}"
                       class="{{ spn_active_link(['income.create']) }}">{{ __('inventory.Add Income') }}</a>
                </li>
            @endif
            @if (permissionCheck('income.index'))
                <li>
                    <a href="{{ route('income.index') }}"
                       class="{{ spn_active_link(['income.index']) }}">{{ __('inventory.Income Lists') }}</a>
                </li>
            @endif

            @if(permissionCheck('bank_accounts.index'))
                <li>
                    <a href="{{ route('bank_accounts.index') }}"
                       class="{{ spn_active_link(['bank_accounts.index']) }}">{{ __('account.Bank Accounts') }}</a>
                </li>
            @endif

            @if(permissionCheck('openning_balance.create'))
                <li>
                    <a href="{{ route('openning_balance.create') }}"
                       class="{{ spn_active_link(['openning_balance.index']) }}">{{ __('account.Opening Balance') }}</a>
                </li>
            @endif
            @if(permissionCheck('char_accounts.index'))
                <li>
                    <a href="{{ route('char_accounts.index') }}"
                       class="{{ spn_active_link(['char_accounts.index']) }}">{{ __('account.Chart Of Accounts') }}</a>
                </li>
            @endif
            @if (Auth::user()->role_id == 1)
                <li>
                    <a href="{{ route('payment_request.index.all') }}"
                    class="{{ spn_active_link(['payment_request.index.all']) }}">{{ __('account.Payment Requests') }}</a>
                </li>
            @endif
            @if (Auth::user()->role_id == 3)
                <li>
                    <a href="{{ route('payment_request.index') }}"
                    class="{{ spn_active_link(['payment_request.index']) }}">{{ __('account.Payment Requests') }}</a>
                </li>
            @endif
            @if (Auth::user()->role_id == 8)
                <li>
                    <a href="{{ route('staff.payment_request.index') }}"
                    class="{{ spn_active_link(['staff.payment_request.index']) }}">{{ __('account.Staff Payment Requests') }}</a>
                </li>
            @endif

            <li class="{{ spn_nav_item_open(['transaction.index', 'transaction.index.cost', 'statement.index', 'profit.index', 'account.balance.index', 'income_by_customer', 'expense_by_supplier', 'sale_tax', 'trial_balance.index', 'balance_sheet.index', 'cost_centers.index'], 'mm-active') }}">
                <a href="javascript:" class="has-arrow" aria-expanded="false">
                    <div class="nav_title">
                        <span>{{ __('account.Report') }}</span>
                    </div>
                </a>
                <ul class="{{request()->is('report/sales-report') || request()->is('report/sales-report/*') ? 'mm-collapse mm-show' : ''}}">


                    @if(permissionCheck('transaction.index'))
                        <li>
                            <a href="{{ route('transaction.index') }}"
                               class="{{ spn_active_link(['transaction.index']) }}">{{ __('account.Transactions') }}</a>
                        </li>
                    @endif
                    @if(permissionCheck('transaction.index.cost'))
                        <li>
                            <a href="{{ route('transaction.index.cost') }}"
                               class="{{ spn_active_link(['transaction.index.cost']) }}">{{ __('account.Cost Report') }}</a>
                        </li>
                    @endif

                    @if(permissionCheck('statement.index'))
                        <li>
                            <a href="{{ route('statement.index') }}"
                               class="{{ spn_active_link(['statement.index']) }}">{{ __('account.Statement') }}</a>
                        </li>
                    @endif

                    @if (permissionCheck('profit.index'))
                        <li>
                            <a href="{{ route('profit.index') }}"
                               class="{{ spn_active_link(['profit.index']) }}">{{ __('report.Profit & Loss') }}</a>
                        </li>
                    @endif

                    @if (permissionCheck('account.balance.index'))
                        <li>
                            <a href="{{ route('account.balance.index') }}"
                               class="{{ spn_active_link(['account.balance.index']) }}">{{ __('account.Account Balance') }}</a>
                        </li>
                    @endif

                    @if (permissionCheck('trial_balance.index'))
                        <li>
                            <a href="{{ route('trial_balance.index') }}"
                               class="{{ spn_active_link(['trial_balance.index']) }}">{{ __('account.Trial Balance') }}</a>
                        </li>
                    @endif

                    @if (permissionCheck('balance_sheet.index'))
                        <li>
                            <a href="{{ route('balance_sheet.index') }}"
                               class="{{ spn_active_link(['balance_sheet.index']) }}">{{ __('account.Balance Sheet') }}</a>
                        </li>
                    @endif

                    @if (permissionCheck('cost_centers.index'))
                        <li>
                            <a href="{{ route('cost_centers.index') }}"
                               class="{{ spn_active_link(['cost_centers.index']) }}">{{ __('account.Cost Centers') }}</a>
                        </li>
                    @endif

                    @if (permissionCheck('income_by_customer'))
                        <li>
                            <a href="{{ route('income_by_customer') }}"
                               class="{{ spn_active_link(['income_by_customer']) }}">{{ __('account.Income By Customer') }}</a>
                        </li>
                    @endif


                    @if (permissionCheck('expense_by_supplier'))
                        <li>
                            <a href="{{ route('expense_by_supplier') }}"
                               class="{{ spn_active_link(['expense_by_supplier']) }}">{{ __('account.Expense By Supplier') }}</a>
                        </li>
                    @endif

                    @if (permissionCheck('sale_tax'))
                        <li>
                            <a href="{{ route('sale_tax') }}"
                               class="{{ spn_active_link(['sale_tax']) }}">{{ __('account.Sales Tax') }}</a>
                        </li>
                    @endif

                </ul>
            </li>

        </ul>
    </li>
@endif

@if(permissionCheck('transfer_showroom.create') || permissionCheck('transfer_showroom.index'))
    <li class="{{ $transfer ?'mm-active' : '' }}">
        <a href="javascript:" class="has-arrow" aria-expanded="false">
            <div class="nav_icon_small">
                <span class="fa fa-bar-chart"></span>
            </div>
            <div class="nav_title">
                <span>{{ __('account.Transfer') }}</span>
            </div>
        </a>
        <ul>
            @if(permissionCheck('transfer_showroom.create'))
                <li>
                    <a href="{{ route('transfer_showroom.create') }}"
                       class="{{ spn_active_link(['transfer_showroom.create']) }}">{{ __('account.Make A Transfer (to Expense)') }}</a>
                </li>
                <li>
                    <a href="{{ route('treasury_transfer.create') }}"
                       class="{{ spn_active_link(['treasury_transfer.create']) }}">{{ __('account.Treasury Transfer (Cash / Bank)') }}</a>
                </li>
            @endif
            @if(permissionCheck('transfer_showroom.index'))
                <li>
                    <a href="{{ route('transfer_showroom.index') }}"
                       class="{{ spn_active_link(['transfer_showroom.index']) }}">{{ __('account.Transfered Lists') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif
