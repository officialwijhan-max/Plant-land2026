<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('category.store'))
            <a data-toggle="modal" data-target="#Item_Details" class="primary-btn radius_30px mr-10 fix-gr-bg" href="#"><i class="ti-plus"></i>{{ __('common.Add Category') }}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('category.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('category.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                    <a class="custom_thead_title" data-id="name" href="#">
                        @if (request('sort') == 'asc' && request('col') == "name")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "name")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Name')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="code" href="#">
                        @if (request('sort') == 'asc' && request('col') == "code")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "code")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Code')}}
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
                        {{__('common.Parent')}}
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
                        {{__('common.Description')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="status" href="#">
                        @if (request('sort') == 'asc' && request('col') == "status")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "status")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Status')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{__('common.Action')}}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $category_value)
            <x-table.tr>
                <x-table.td>
                    <a href="#">
                        @if ($category_value->level > 0)
                            @for ($i = 1; $i < $category_value->level; $i++)
                                -
                            @endfor
                        ->
                        @endif
                        {{ $category_value->name }}
                    </a>
                </x-table.td>
                <x-table.td>{{ $category_value->code }}</x-table.td>
                <x-table.td>{{ @$category_value->parentCat->name ?? 'N/A' }}</x-table.td>
                <x-table.td>{{ $category_value->description }}</x-table.td>
                <x-table.td>
                    @if($category_value->status == 1)
                        <span class="badge_1">{{__('common.Active')}}</span>
                    @else
                        <span class="badge_4">{{__('common.DeActive')}}</span>
                    @endif
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenu{{ $loop->iteration  + 1 }}" data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            {{__('common.Select')}}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right"
                             aria-labelledby="dropdownMenu{{ $loop->iteration  + 1 }}">
                            @if(permissionCheck('category.edit'))
                                <a href="#" data-toggle="modal" data-target="#Item_Edit"
                                   class="dropdown-item edit_category"
                                   data-value="{{$category_value->id}}" type="button">{{__('common.Edit')}}</a>
                            @endif

                            @if(permissionCheck('category.delete') && $category_value->products->count() == 0)

                                <a onclick="confirm_modal('{{route('category.delete',$category_value->id)}}');" class="dropdown-item ">{{__('common.Delete')}}</a>

                            @endif
                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @if(count($category_value->categories) > 0)
                @include('product::category.hierarchy',['subcategories' => $category_value->categories])
            @endif
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
