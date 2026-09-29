<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('events.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('events.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                        {{ __('event.Title') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="title" href="#">
                        @if (request('sort') == 'asc' && request('col') == "title")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "title")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('event.for_whom') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="for_whom" href="#">
                        @if (request('sort') == 'asc' && request('col') == "for_whom")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "for_whom")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('event.Start Date') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="from_date" href="#">
                        @if (request('sort') == 'asc' && request('col') == "from_date")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "from_date")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('event.To Date') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="to_date" href="#">
                        @if (request('sort') == 'asc' && request('col') == "to_date")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "to_date")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('event.Location') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{__('common.Action')}}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $item)
            <x-table.tr>
                <x-table.td><a href="#">{{ $item->title }}</a> </x-table.td>
                <x-table.td>{{ $item->for_whom }}</x-table.td>
                <x-table.td>{{ $item->from_date }}</x-table.td>
                <x-table.td>{{ $item->to_date }}</x-table.td>
                <x-table.td>{{ $item->location }}</x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button
                            class="btn btn-secondary dropdown-toggle"
                            type="button"
                            id="dropdownMenu2"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            {{__('common.Select')}}
                        </button>
                        <div
                            class="dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownMenu2">
                            @if(permissionCheck('events.edit'))
                                <a class="dropdown-item"
                                   href="{{route('events.edit',$item->id)}}">@lang('event.Edit')</a>
                            @endif

                            @if(permissionCheck('events.delete'))
                                <a onclick="confirm_modal('{{route('events.delete', $item->id)}}')"
                                   class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                            @endif
                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
