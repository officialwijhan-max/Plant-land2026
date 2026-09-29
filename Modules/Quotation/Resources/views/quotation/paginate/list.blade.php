<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('quotation.store'))
            <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("quotation.create")}}"><i class="ti-plus"></i>{{__('quotation.New Quotation')}}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('quotation.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('quotation.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                        {{__('common.No')}}
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
                        {{__('quotation.Date')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="invoice_no" href="#">
                        @if (request('sort') == 'asc' && request('col') == "invoice_no")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "invoice_no")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('quotation.Reference No')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="customer_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "customer_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "customer_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('quotation.Customer')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="quotationable_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "quotationable_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "quotationable_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('quotation.Branch')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="user_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "user_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "user_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('quotation.User')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="convert_status" href="#">
                        @if (request('sort') == 'asc' && request('col') == "convert_status")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "convert_status")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Convert Status')}}
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
                <x-table.td><a href="#">{{ showDate($item->date) }}</a> </x-table.td>
                <x-table.td><a href="{{route('quotation.show',$item->id)}}" target="_blank" class="pointer">{{$item->invoice_no}}</a></x-table.td>
                <x-table.td>{{@$item->customer->name}}</x-table.td>
                <x-table.td>{{ @$item->quotationable->name }}</x-table.td>
                <x-table.td>{{ $item->user->name }}</x-table.td>
                <x-table.td>
                    @if ($item->convert_status == 0)
                        <h6><span class="badge_4">{{__('common.Pending')}}</span></h6>
                    @else
                        <h6><span class="badge_1">{{__('quotation.Converted To Sale')}}</span></h6>
                    @endif
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('common.Select') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            @if ($item->status != 1)
                                @if(permissionCheck('quotation.edit'))
                                    <a href="{{ route('quotation.edit', $item->id) }}" class="dropdown-item" type="button">{{ __('common.Edit') }}</a>
                                @endif
                            @else
                                <a href="#" class="dropdown-item" type="button">{{ __('quotation.Mail Sent') }}</a>
                            @endif
                            <a href="{{ route('quotation.show', $item->id) }}" class="dropdown-item" type="button">{{ __('quotation.Details View') }}</a>
                            @if ($item->convert_status == 0)
                                <a href="{{ route('quotation.convert', $item->id) }}" class="dropdown-item" type="button">{{ __('quotation.Convert To Sale') }}</a>
                            @else
                                <a class="dropdown-item" type="button">{{ __('quotation.Converted To Sale') }}</a>
                            @endif
                            <a href="{{ route('quotation.order.pdf', $item->id) }}" class="dropdown-item" type="button">{{ __('quotation.Download') }}</a>
                            <a href="{{ route('quotation.clone', $item->id) }}" class="dropdown-item" type="button">{{ __('quotation.Clone to Quotation') }}</a>
                            <a href="javascript:void(0);" class="dropdown-item" type="button" data-toggle="modal" data-id="{{ $item->id }}" data-target="#cloneToProjectModal">{{ __('quotation.Clone to Project') }}</a>
                            @if(permissionCheck('quotation.delete'))
                                <a onclick="confirm_modal('{{ route('quotation.delete', $item->id) }}')" class="dropdown-item edit_brand">{{ __('common.Delete') }}</a>
                            @endif
                        </div>
                    </div>
                </x-table.td>
                
                <!-- Modal -->
                <div class="modal fade" id="cloneToProjectModal" tabindex="-1" role="dialog" aria-labelledby="cloneToProjectModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('quotation.cloneToProject') }}" method="POST" id="cloneToProjectForm">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cloneToProjectModalLabel">{{ __('Clone to Project') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="quotation_id" id="quotation_id">
                                    <div class="form-group">
                                        <label for="teamSelect">{{ __('Select Team') }}</label>
                                        <select name="team_id" id="teamSelect" class="primary_singleSelect form-control" required>
                                            <option value="">{{ __('Select Team') }}</option>
                                            <!-- Dynamically populate teams -->
                                            @foreach ($teams as $team)
                                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>                
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
       
        // Modal event listener
        $('#cloneToProjectModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var quotationId = button.data('id'); // Extract info from data-id attribute

            // Update the modal's form with the quotation ID
            var modal = $(this);
            modal.find('#quotation_id').val(quotationId);
        });
    });
</script>



