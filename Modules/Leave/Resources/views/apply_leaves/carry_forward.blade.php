@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('leave.Carry Forward') }}</h3>

                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('leave::apply_leaves.paginates.cf_list')
                            </div>
                            {{-- <table class="table Crm_table_active3">
                                <thead>
                                <tr>
                                    <th scope="col">{{ __('common.Sl') }}</th>
                                    <th scope="col">{{ __('common.Type') }}</th>
                                    <th scope="col">{{ __('common.Name') }}</th>
                                    <th scope="col">{{ __('common.Username') }}</th>
                                    <th scope="col">{{ __('common.Email') }}</th>
                                    <th scope="col">{{ __('leave.Carry Forward') }}</th>
                                    <th scope="col">{{ __('common.Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $key => $user)
                                    <tr>
                                        @php
                                            $staff = $user->staff;
                                            $carry_forward = $staff ?  $staff->carry_forward : 0;
                                            $status = $staff ?  $staff->is_carry_active : 0;
                                        @endphp
                                        <th>{{ $key+1 }}</th>
                                        <td>{{str_replace('_', ' ', @$user->role->type)}}</td>
                                        <td>{{ @$user->name }}</td>
                                        <td>{{ @$user->username }}</td>
                                        <td><a href="mailto:{{ @$user->email }}">{{ @$user->email }}</a></td>
                                        <td>{{$carry_forward}} ({{__('leave.Available')}} :
                                            {{$user->CarryForward - $carry_forward}})
                                        </td>
                                        <td>
                                            <label class="switch_toggle" for="active_checkbox{{ $user->id }}">
                                                <input type="checkbox" id="active_checkbox{{ $user->id }}"
                                                       {{$status == 1 ? 'checked' : ''}}
                                                       value="{{ $user->CarryForward }}"
                                                       onchange="update_active_status(this,{{$staff ? $staff->id : ''}})" {{ permissionCheck('carry.forward') ? '' : 'disabled' }}>
                                                <div class="slider round"></div>
                                            </label>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="getDetails">
    </div>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'carry_forward_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('common.Name') }}','staff'],['3','{{ __('common.Username') }}','username'],['4','{{ __('common.Email') }}','email'],['5','{{ __('leave.Carry Forward') }}','carry_forward'],['6','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['carry_forward_list']) }}">

    @include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script type="text/javascript">
    function update_active_status(el, id) {
        if (el.checked) {
            var status = 1;
        } else {
            var status = 0;
        }
        $.post('{{ route('carry.forward.update') }}', {
            _token: '{{ csrf_token() }}',
            day: el.value,
            id: id,
            status: status
        }, function (data) {
            toastr.success(data);
        });
    }
</script>
@endpush
