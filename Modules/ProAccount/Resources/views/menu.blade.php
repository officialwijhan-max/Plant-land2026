@php
    $account = false;
    $cashbook = false;
    $expenses = false;
    $income = false;

    if(request()->is('pro-account/*'))
    {
        $account = true;
    }
    if(request()->is('cashbooks'))
    {
        $cashbook = true;
    }
@endphp

@if (permissionCheck('cashbook.index'))
    <li class="@isset($cashbook){{ ($cashbook) ?'mm-active' : '' }} @endisset">
        <a href="{{ route('cashbook.index') }}" class="{{request()->is('cashbook') ? 'active' : ''}}" aria-expanded="false">
            <div class="nav_icon_small">
                <span class="fas fa-book"></span>
            </div>
            <div class="nav_title">
                <span>{{ __('account.Cashbook') }}</span>
            </div>
        </a>
    </li>
@endif

@if (permissionCheck('account_module'))
    <li class="{{ $account ?'mm-active' : '' }} sortable_li">
        <a href="javascript:void(0);" class="has-arrow" aria-expanded="{{ $account ? 'true' : 'false' }}">
            <div class="nav_icon_small">
                <span class="fa fa-calculator"></span>
            </div>
            <div class="nav_title">
                <span>{{ trans('account.pro_accounting') }}</span>
            </div>
        </a>
        <ul id="account-menu">
            <li class="{{request()->routeIs('leadger.index') || request()->routeIs('sub_leadger.index') || request()->routeIs('cash_flow_account.index') ? 'mm-active' : ''}}">
                <a href="javascript:;" class="has-arrow" aria-expanded="false">
                    <div class="nav_title">
                        <span>{{ trans('account.account') }}</span>
                    </div>
                </a>
                <ul class="{{request()->routeIs('leadger.index') || request()->routeIs('sub_leadger.index') || request()->routeIs('cash_flow_account.index') ? 'mm-collapse' : ''}}">
                    @if (permissionCheck('leadger.index') || saasPermissionCheck('leadger.index'))
                        <li>
                            <a href="{{ route('leadger.index') }}" class="anchor_menu {{request()->routeIs('leadger.*') ? 'active' : ''}}">
                                {{ trans('account.chart_of_accounts') }}
                            </a>
                        </li>
                    @endif
                    @if (permissionCheck('sub_leadger.index') || saasPermissionCheck('sub_leadger.index'))
                        <li>
                            <a href="{{ route('sub_leadger.index') }}" class="anchor_menu {{request()->routeIs('sub_leadger.*') ? 'active' : ''}}">
                                {{ trans('account.partner_account') }}
                            </a>
                        </li>
                    @endif
                    @if (((permissionCheck('cash_flow_account.index') || saasPermissionCheck('cash_flow_account.index'))) && Settings('use_cash_flow_in_accounting') == 1)
                        <li>
                            <a href="{{ route('cash_flow_account.index') }}" class="anchor_menu {{request()->routeIs('cash_flow_account.*') ? 'active' : ''}}">
                                {{ trans('account.cash_flow_account') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            @if (permissionCheck('revievable_sub_menu') || saasPermissionCheck('revievable_sub_menu'))
                <li class="{{request()->routeIs('account.recievable_account') || request()->routeIs('vouchers.recieve_index') || request()->routeIs('vouchers.recieve_create') || request()->routeIs('vouchers.recieve_edit') || request()->routeIs('vouchers.recieve_money_reciept') ? 'mm-active' : ''}}">
                    <a href="javascript:;" class="has-arrow" aria-expanded="false">
                        <div class="nav_title">
                            <span>{{ trans('account.recievable') }}</span>
                        </div>
                    </a>
                    <ul class="{{request()->routeIs('account.recievable_account') || request()->routeIs('vouchers.recieve_index') || request()->routeIs('vouchers.recieve_create') || request()->routeIs('vouchers.recieve_edit') || request()->routeIs('vouchers.recieve_money_reciept') ? 'mm-collapse mm-show' : ''}}">
                        @if (permissionCheck('account.recievable_account') || saasPermissionCheck('cash_flow_account.index'))
                            <li>
                                <a href="{{ route('account.recievable_account') }}" class="anchor_menu {{request()->routeIs('account.recievable_account') ? 'active' : ''}}">
                                    {{ trans('account.customer') }}
                                </a>
                            </li>
                        @endif
                        @if (permissionCheck('vouchers.recieve_index') || saasPermissionCheck('cash_flow_account.index'))
                            <li>
                                <a href="{{ route('vouchers.recieve_index') }}" class="anchor_menu {{(request()->routeIs('vouchers.recieve_index') || request()->routeIs('vouchers.recieve_create') || request()->routeIs('vouchers.recieve_edit') || request()->routeIs('vouchers.recieve_money_reciept')) ? 'active' : ''}}">
                                    {{ trans('account.voucher_recieve') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (permissionCheck('payable_sub_menu') || saasPermissionCheck('payable_sub_menu'))
                <li class="{{request()->routeIs('account.payable_account') || request()->routeIs('vouchers.index') || request()->routeIs('vouchers.create') || request()->routeIs('vouchers.edit') ? 'mm-active' : ''}}">
                    <a href="javascript:;" class="has-arrow" aria-expanded="false">
                        <div class="nav_title">
                            <span>{{ trans('account.payable') }}</span>
                        </div>
                    </a>
                    <ul class="{{request()->routeIs('account.payable_account') || request()->routeIs('vouchers.index') || request()->routeIs('vouchers.create') || request()->routeIs('vouchers.edit') ? 'mm-collapse mm-show' : ''}}">
                        @if (permissionCheck('account.payable_account') || saasPermissionCheck('account.payable_account'))
                            <li>
                                <a href="{{ route('account.payable_account') }}" class="anchor_menu {{request()->routeIs('account.payable_account') ? 'active' : ''}}">
                                    {{ trans('account.supplier') }}
                                </a>
                            </li>
                        @endif
                        @if (permissionCheck('vouchers.index') || saasPermissionCheck('vouchers.index'))
                            <li>
                                <a href="{{ route('vouchers.index') }}" class="anchor_menu {{(request()->routeIs('vouchers.index') || request()->routeIs('vouchers.create') || request()->routeIs('vouchers.edit')) ? 'active' : ''}}">
                                    {{ trans('account.voucher_payment') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (permissionCheck('journal_sub_menu') || saasPermissionCheck('journal_sub_menu'))
                <li class="{{request()->routeIs('journal.*') || request()->routeIs('transaction.transactions') || request()->routeIs('voucher_approval.*') ? 'mm-active' : ''}}">
                    <a href="javascript:;" class="has-arrow" aria-expanded="false">
                        <div class="nav_title">
                            <span>{{ trans('account.journal') }}</span>
                        </div>
                    </a>
                    <ul class="{{request()->routeIs('journal.*') || request()->routeIs('transaction.transactions') || request()->routeIs('voucher_approval.*') ? 'mm-collapse mm-show' : ''}}">
                        @if (permissionCheck('journal.index') || saasPermissionCheck('journal.index'))
                            <li>
                                <a href="{{ route('journal.index') }}" class="anchor_menu {{request()->routeIs('journal.*') ? 'active' : ''}}">
                                    {{ trans('account.journal') }}
                                </a>
                            </li>
                        @endif
                        @if (permissionCheck('voucher_approval.index') || saasPermissionCheck('voucher_approval.index'))
                            <li>
                                <a href="{{ route('voucher_approval.index') }}" class="anchor_menu {{request()->routeIs('voucher_approval.*') ? 'active' : ''}}">
                                    {{ trans('account.voucher_approval') }}
                                </a>
                            </li>
                        @endif
                        @if (permissionCheck('transaction.transactions') || saasPermissionCheck('transaction.transactions'))
                            <li>
                                <a href="{{ route('transaction.transactions') }}" class="anchor_menu {{request()->routeIs('transaction.transactions') ? 'active' : ''}}">
                                    {{ trans('account.transactions') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(permissionCheck('pro-income.index'))
                <li>
                    <a href="{{ route('pro-income.index') }}" class="{{request()->routeIs('pro-income.*') ? 'active' : ''}}">{{ __('account.Income') }}</a>
                </li>
            @endif
            @if(permissionCheck('pro-expenses.index'))
                <li>
                    <a href="{{route('pro-expenses.index')}}" class="{{request()->routeIs('pro-expenses.*') ? 'active' : ''}}">{{ __('account.Expense') }}</a>
                </li>
            @endif
            @if(permissionCheck('pro-opening-balance.index'))
                <li>
                    <a href="{{route('pro-opening-balance.index')}}" class="{{request()->routeIs('pro-opening-balance.*') ? 'active' : ''}}">{{ __('account.Opening Balance') }}</a>
                </li>
            @endif

            @if (permissionCheck('banking_statement.index'))
                <li>
                    <a href="{{ route('banking_statement.index') }}" class="anchor_menu {{request()->routeIs('banking_statement.*') ? 'active' : ''}}">
                        {{ trans('account.banking') }}
                    </a>
                </li>
            @endif

            <li class="{{request()->routeIs('leadger_report.*') || request()->routeIs('balance_sheet_report') || request()->routeIs('income_statement_report') || request()->routeIs('vouchers.cash_flow') || request()->routeIs('vouchers.trial_balance') || request()->routeIs('vouchers.trial_balance.import_view') ? 'mm-active' : ''}}">
                <a href="javascript:;" class="has-arrow" aria-expanded="false">
                    <div class="nav_title">
                        <span>{{ trans('account.report') }}</span>
                    </div>
                </a>
                <ul class="{{request()->routeIs('leadger_report.leadger_report_view') || request()->routeIs('balance_sheet_report') || request()->routeIs('income_statement_report') || request()->routeIs('leadger_report.sub_leadger_report_view') || request()->routeIs('vouchers.cash_flow') || request()->routeIs('vouchers.trial_balance') ? 'mm-collapse mm-show' : ''}}">
                    @if (permissionCheck('leadger_report.leadger_report_view'))
                        <li>
                            <a href="{{ route('leadger_report.leadger_report_view') }}" class="anchor_menu {{request()->routeIs('leadger_report.leadger_report_view') ? 'active' : ''}}">
                                {{ trans('account.leadger_report') }}
                            </a>
                        </li>
                    @endif
                    @if (permissionCheck('leadger_report.sub_leadger_report_view'))
                        <li>
                            <a href="{{ route('leadger_report.sub_leadger_report_view') }}" class="anchor_menu {{request()->routeIs('leadger_report.sub_leadger_report_view') ? 'active' : ''}}">
                                {{ trans('account.partner_ledger_reports') }}
                            </a>
                        </li>
                    @endif
                    @if (permissionCheck('leadger_report.partner_summary_reports'))
                        <li>
                            <a href="{{ route('leadger_report.partner_summary_reports') }}" class="anchor_menu {{request()->routeIs('leadger_report.partner_summary_reports') ? 'active' : ''}}">
                                {{ trans('account.partner_summary_reports') }}
                            </a>
                        </li>
                    @endif
                    {{-- @if ((permissionCheck('vouchers.cash_flow') || saasPermissionCheck('vouchers.cash_flow')))
                        <li>
                            <a href="{{ route('vouchers.cash_flow') }}" class="anchor_menu {{request()->routeIs('vouchers.cash_flow') ? 'active' : ''}}">
                                {{ trans('account.cash_flow') }}
                            </a>
                        </li>
                    @endif --}}
                    @if (permissionCheck('vouchers.trial_balance'))
                        <li>
                            <a href="{{ route('vouchers.trial_balance') }}" class="anchor_menu {{request()->routeIs('vouchers.trial_balance') || request()->routeIs('vouchers.trial_balance.import_view') ? 'active' : ''}}">
                                {{ trans('account.trial_balance') }}
                            </a>
                        </li>
                    @endif
                    @if (permissionCheck('income_statement_report'))
                        <li>
                            <a href="{{ route('income_statement_report') }}" class="anchor_menu {{request()->routeIs('income_statement_report') ? 'active' : ''}}">
                                {{ trans('account.income_statement') }}
                            </a>
                        </li>
                    @endif
                    @if (permissionCheck('balance_sheet_report'))
                        <li>
                            <a href="{{ route('balance_sheet_report') }}" class="anchor_menu {{request()->routeIs('balance_sheet_report') ? 'active' : ''}}">
                                {{ trans('account.balance_sheet') }}
                            </a>
                        </li>
                    @endif
                    @if (permissionCheck('profit_loss_single_entry_report') && Settings('accounting_entry_system') == "single_entry")
                        <li>
                            <a href="{{ route('profit_loss_single_entry_report') }}" class="anchor_menu {{request()->routeIs('profit_loss_single_entry_report') ? 'active' : ''}}">
                                {{ trans('account.profit_loss') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            @if (permissionCheck('account_configuration_permit') || saasPermissionCheck('account_configuration_permit'))
                <li class="{{request()->routeIs('account.configuration') || request()->routeIs('account.report.configuration') || request()->routeIs('financial_years.index') ? 'mm-active' : ''}}">
                    <a href="javascript:;" class="has-arrow" aria-expanded="false">
                        <div class="nav_title">
                            <span>{{ trans('account.configuration') }}</span>
                        </div>
                    </a>
                    <ul class="{{request()->routeIs('account.configuration_entry_system') ? 'mm-collapse mm-show' : ''}}">
                        {{-- @if (permissionCheck('account.configuration_entry_system'))
                            <li>
                                <a href="{{ route('account.configuration_entry_system') }}" class="anchor_menu {{request()->routeIs('account.configuration_entry_system') ? 'active' : ''}}">
                                    {{ trans('account.entry_system') }}
                                </a>
                            </li>
                        @endif --}}
                        @if ((permissionCheck('account.configuration') || saasPermissionCheck('account.configuration')) && Settings('accounting_entry_system') != "single_entry")
                            <li>
                                <a href="{{ route('account.configuration') }}" class="anchor_menu {{request()->routeIs('account.configuration') ? 'active' : ''}}">
                                    {{ trans('account.configuration') }}
                                </a>
                            </li>
                        @endif
                        @if (permissionCheck('account.report.configuration') || saasPermissionCheck('account.report.configuration'))
                            <li>
                                <a href="{{ route('account.report.configuration') }}" class="anchor_menu {{request()->routeIs('account.report.configuration') ? 'active' : ''}}">
                                    {{ trans('account.report') }}
                                </a>
                            </li>
                        @endif
                        @if ((permissionCheck('financial_years.index') || saasPermissionCheck('financial_years.index')) && Settings('accounting_entry_system') != "single_entry")
                            <li>
                                <a href="{{ route('financial_years.index') }}" class="anchor_menu {{request()->routeIs('financial_years.*') ? 'active' : ''}}">
                                    {{ trans('account.financial_years') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
    </li>
@endif
