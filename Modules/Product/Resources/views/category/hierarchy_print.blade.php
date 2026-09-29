
@foreach($subcategories as $category_value)
    @if ($permissions)
        <tr>
            @if (str_contains($permissions->export_column, 'name'))
                <td>
                    @if ($category_value->level > 0)
                        @for ($i = 1; $i < $category_value->level; $i++)
                            -
                        @endfor
                    ->
                    @endif
                    {{ $category_value->name }}
                </td>
            @endif
            @if (str_contains($permissions->export_column, 'code'))
                <td>{{ $category_value->code }}</td>
            @endif
            @if (str_contains($permissions->export_column, 'parent'))
                <td>{{ @$category_value->parentCat->name ?? 'N/A' }} </td>
            @endif
            @if (str_contains($permissions->export_column, 'description'))
                <td>{{ $category_value->description }}</td>
            @endif
            @if (str_contains($permissions->export_column, 'status'))
                <td>
                    @if($category_value->status == 1)
                        {{__('common.Active')}}
                    @else
                        {{__('common.DeActive')}}
                    @endif
                </td>
            @endif
        </tr>
    @else
        <tr>
            <td>
                @if ($category_value->level > 0)
                    @for ($i = 1; $i < $category_value->level; $i++)
                        -
                    @endfor
                ->
                @endif
                {{ $category_value->name }}
            </td>
            <td>{{ $category_value->code }}</td>
            <td>{{ @$category_value->parentCat->name ?? 'N/A' }} </td>
            <td>{{ $category_value->description }}</td>
            <td>
                @if($category_value->status == 1)
                    {{__('common.Active')}}
                @else
                    {{__('common.DeActive')}}
                @endif
            </td>
        </tr>
    @endif
    @if(count($category_value->categories) > 0)
        @include('product::category.hierarchy_print',['subcategories' => $category_value->categories, 'permissions' => $permissions])
    @endif
@endforeach