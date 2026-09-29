<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('financial_years.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('financial_years.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                    <a class="custom_thead_title" data-id="start_date" href="#">
                        @if (request('sort') == 'asc' && request('col') == "start_date")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "start_date")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.start_date')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="end_date" href="#">
                        @if (request('sort') == 'asc' && request('col') == "end_date")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "end_date")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.end_date')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="is_locked" href="#">
                        @if (request('sort') == 'asc' && request('col') == "is_locked")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "is_locked")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.is_locked')}}
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
                <x-table.td>{{ showDate($item->start_date) }}</x-table.td>
                <x-table.td>{{ ($item->end_date) ? showDate($item->end_date) : "" }}</x-table.td>
                <x-table.td>
                    @if ($item->is_locked == 0)
                        <span class="badge_3">{{__('account.Open')}}</span>
                    @else
                        <span class="badge_1">{{__('account.Closed')}}</span>
                    @endif
                </x-table.td>
                <x-table.td>
                    <a class="primary-btn radius_30px mr-10 fix-gr-bg close_now_btn" data-id='{{ $item->id }}'>{{ trans('account.close_now') }}</a>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
