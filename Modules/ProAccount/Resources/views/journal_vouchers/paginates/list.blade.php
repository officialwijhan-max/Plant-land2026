<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('journal.create') && Settings('accounting_entry_system') != "single_entry")
            <a class="primary-btn radius_30px mr-10 create_link fix-gr-bg" href="{{ route('journal.create') }}"><i class="ti-plus"></i>{{ trans('account.add_new_journal') }}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('journal.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('journal.index').'?import_as=csv'}}" target="_blank" title="Export">
            <i class="ti-export"></i>
        </a>
        <a href="#" title="Col Show/Hide" class="hide_show_click_btn">
            <i class="ti-layout-column3"></i>
        </a>
    </x-slot>
    <x-slot name="table">
        <x-table.head>
            <tr>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Sl')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="date" href="#">
                        @if (request('sort') == 'asc' && request('col') == "date")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "date")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.date')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="txn_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "txn_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "txn_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.txn_id')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.reference_no')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="amount" href="#">
                        @if (request('sort') == 'asc' && request('col') == "amount")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "amount")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.amount')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="is_approve" href="#">
                        @if (request('sort') == 'asc' && request('col') == "is_approve")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "is_approve")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.approved')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                    class="ti-arrow-down"></i>{{__('common.Action')}}</a> </x-table.th>
            </tr>
        </x-table.head>
        <x-table.body>
            @foreach ($items as $key => $item)
            <x-table.tr>
                <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                <x-table.td>{{ showDate($item->date) }}</x-table.td>
                <x-table.td><a class="voucher_detail pointer" data-id='{{ $item->id }}'>{{ $item->GetTypeName()." - ".$item->txn_id }}</a></x-table.td>
                <x-table.td>{{ $item->narration }}</x-table.td>
                <x-table.td>{{ single_price($item->amount) }}</x-table.td>
                <x-table.td>
                    @if ($item->is_approve == 0)
                        <span class="badge_3">{{ trans("account.pending") }}</span>
                    @elseif ($item->is_approve == 1)
                        <span class="badge_1">{{ trans("account.approved") }}</span>
                    @else
                        <span class="badge_4">{{ trans("account.cancelled") }}</span>
                    @endif
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenu2" data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            {{ __('common.Select') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            @if (permissionCheck('get_voucher_details'))
                                <a class="dropdown-item voucher_detail" data-id='{{ $item->id }}'>{{trans('account.view')}}</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('vouchers.print', $item->id) }}" target="_blank">{{trans('account.print')}}</a>
                            @if ($item->is_approve == 1 && ($item->type == "rec_cash" || $item->type == "rec_bank"))
                                <a href="{{ route('vouchers.recieve_money_reciept', $item->id) }}" target="_blank" class="dropdown-item">{{trans('account.money_reciept')}}</a>
                            @endif
                            @if ($item->is_approve == 1 && ($item->type == "pay_cash" || $item->type == "pay_bank"))
                                <a href="{{ route('vouchers.payment_money_reciept', $item->id) }}" target="_blank" class="dropdown-item">{{trans('account.money_reciept')}}</a>
                            @endif
                            @if ($item->is_approve == 2 && permissionCheck('undo_from_cancelled'))
                                <a href="#" class="dropdown-item undo_btn" data-id="{{ $item->id }}">{{trans('account.back_to_pending')}}</a>
                            @endif
                            @if ($item->is_approve != 1)
                                @if ($item->is_manual_entry == 0)
                                    <a class="dropdown-item">{{trans('account.not_editable_or_deletable_from_here')}}</a>
                                @else
                                    @if (permissionCheck('journal.edit') && $item->sale_or_purchase != "exp")
                                        <a href="{{ route('journal.edit', $item->id) }}" class="dropdown-item">{{trans('account.edit')}}</a>
                                    @endif
                                    @if (permissionCheck('vouchers.destroy'))
                                        <a class="dropdown-item delete_leadger" data-id="{{ $item->id }}" type="button">{{ __('common.Delete') }}</a>
                                    @endif
                                @endif
                            @elseif ($item->is_approve == 1 && permissionCheck('vouchers.destroy_approved') && $item->is_manual_entry == 1 && app('financial_year')->id == $item->transactions->first()->accounting_period_id)
                                <a class="dropdown-item delete_approved_leadger" data-id="{{ $item->id }}" type="button">{{ __('common.Delete') }}</a>
                            @endif
                            @if (permissionCheck('journal.audit_history'))
                                <a href="{{route('journal.audit_history',$item->id)}}" class="dropdown-item" type="button">{{trans('account.audit_history')}}</a>
                            @endif
                            @if (permissionCheck('journal.transaction_detail'))
                                <a href="{{route('journal.transaction_detail',$item->id)}}" class="dropdown-item" type="button">{{trans('account.journal_transaction')}}</a>
                            @endif
                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
