<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('permission.roles.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('permission.roles.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                        {{ __('common.SL') }}
                    </a>
                </x-table.th>
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
                        {{ __('role.Role') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="40%"> <a class="custom_thead_title" href="#"> <i
                            class="ti-arrow-down"></i>{{__('common.Action')}}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $item)
                <x-table.tr>
                    <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                    <x-table.td>{{ @$item->name }}</x-table.td>
                    <x-table.td>
                        <!-- shortby  -->
                        <div class="dropdown CRM_dropdown d-inline">
                            <button class="btn btn-secondary dropdown-toggle mt-1"
                                    type="button" id="dropdownMenu2"
                                    data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                {{ __('common.Select') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                                @if(permissionCheck('permission.roles.edit'))
                                    <a href="{{ route('permission.roles.edit',$item->id) }}"
                                    class="dropdown-item"
                                    type="button">@lang('common.Edit')</a>
                                @endif
                                @if ($item->id > 6)
                                    @if(permissionCheck('permission.roles.destroy'))
                                        <a onclick="confirm_modal('{{route('permission.roles.delete', $item->id)}}');" class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                                    @endif
                                @else
                                    <a href="javascript:void(0)" class="dropdown-item"> @lang('common.System Role') </a>
                                @endif
                            </div>
                        </div>
                        <!-- shortby  -->
                        @if(@$item->id != 1)
                            <a href="{{ route('permission.permissions.index', [ 'id' => @$item->id])}}" class="">
                                <button type="button" class="primary-btn small fix-gr-bg mt-1"> {{ __('common.Assign Permission') }}</button>
                            </a>
                        @endif
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
