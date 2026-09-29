
@foreach($subcategories as $subcategory)
    <x-table.tr>
        <x-table.td>
            <a href="#">
                @if ($subcategory->level > 0)
                    @for ($i = 1; $i < $subcategory->level; $i++)
                        -
                    @endfor
                ->
                @endif
                {{ $subcategory->name }}
            </a>
        </x-table.td>
        <x-table.td>{{ $subcategory->code }}</x-table.td>
        <x-table.td>{{ @$subcategory->parentCat->name ?? 'N/A' }}</x-table.td>
        <x-table.td>{{ $subcategory->description }}</x-table.td>
        <x-table.td>
            @if($subcategory->status == 1)
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
                           data-value="{{$subcategory->id}}" type="button">{{__('common.Edit')}}</a>
                    @endif

                    @if(permissionCheck('category.delete') && $subcategory->products->count() == 0)

                        <a onclick="confirm_modal('{{route('category.delete',$subcategory->id)}}');" class="dropdown-item ">{{__('common.Delete')}}</a>

                    @endif
                </div>
            </div>
        </x-table.td>
    </x-table.tr>

    @if(count($subcategory->categories) > 0)
        @include('product::category.hierarchy',['subcategories' => $subcategory->categories])
    @endif
@endforeach
