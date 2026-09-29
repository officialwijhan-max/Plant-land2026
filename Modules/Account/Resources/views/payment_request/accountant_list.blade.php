<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if (Auth::user()->role_id == 8)
            <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{ route('staff.payment_request.create') }}"><i class="ti-plus"></i>New Payment</a>     
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        @if (Auth::user()->role_id == 8)
            <a href="{{route('staff.payment_request.index').'?import_as=print'}}" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{route('staff.payment_request.index').'?import_as=csv'}}" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @else
            <a href="{{route('payment_request.index.all').'?import_as=print'}}" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{route('payment_request.index.all').'?import_as=csv'}}" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @endif
        
        
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
                        {{ __('common.ID') }}
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
                         {{ __('common.Staff Name') }}
                    </a>
                </x-table.th>
                @if (Auth::user()->role_id == 1)
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
                        
                        {{ __('common.Accountant Name') }}
                    </a>
                </x-table.th>    
                @endif
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
                        
                        {{ __('common.Bank Account') }}
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
                        {{ __('account.Date') }}
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
                        
                        {{ __('common.Narration') }}
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
                        
                        {{ __('common.Region') }}
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
                        {{ __('common.Amount') }}
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
                        
                        {{ __('common.Status') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    {{ __('common.Action') }}
                    
                </x-table.th>
                
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $item)
            <x-table.tr>
                <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                <x-table.td>{{ @$item->staff->name }}</x-table.td>
                @if (Auth::user()->role_id == 1)
                    <x-table.td>{{ @$item->accountant->name }}</x-table.td>
                @endif
                <x-table.td>{{ @$item->bank->bank_name ?? '---' }}</x-table.td>
                <x-table.td>{{ date("d F, Y", strtotime($item->created_at)) }}</x-table.td>
                <x-table.td>{{ $item->narration ?? '---' }}</x-table.td>
                <x-table.td>{{ $item->region ?? '---' }}</x-table.td>
                <x-table.td>{{ single_price(@$item->amount) }}</x-table.td>
                <x-table.td>
                    @if (@$item->status == 'pending')
                        <span class="badge_3">{{__('account.Pending')}}</span>
                    @elseif (@$item->status == 'accepted')
                        <span class="badge_1">{{__('account.Approved')}}</span>
                    @else
                        <span class="badge_4">{{__('common.Cancelled')}}</span>
                    @endif
                </x-table.td>
                <x-table.td>
                    @if (@$item->status == 'pending')
                        <div>
                            {{-- <a href="{{ route('payment.approve', $item->id) }}" class="primary-btn radius_30px mr-10 fix-gr-bg">{{ __('Approve') }}</a> --}}
                            <button type="button" class="primary-btn radius_30px mr-10 mb-2 fix-gr-bg" data-toggle="modal" data-target="#acceptModal{{ $item->id }}">{{ __('Approve') }}</button>
                            <button type="button" class="primary-btn radius_30px mr-10 fix-gr-bg" data-toggle="modal" data-target="#rejectModal{{ $item->id }}">{{ __('Reject') }}</button>
                        </div>
        
                        <!-- Acceptance Modal -->
                        <div class="modal fade" id="acceptModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="acceptModalLabel">{{ __('Approve Payment Request') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('payment.approve', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="accept_reason">{{ __('Select Bank Account') }}</label>
                                                <select name="bank_id" id="" required class="form-control">
                                                    <option value="">Select Bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <button type="submit" class="btn btn-success">{{ __('Approve') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel">{{ __('Reject Payment Request') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('payment.reject', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="rejection_reason">{{ __('Reason for rejection') }}</label>
                                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <button type="submit" class="btn btn-danger">{{ __('Reject') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </x-table.td>
                
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>
</x-table>
