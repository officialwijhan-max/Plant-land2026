@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">

        @include('leave::leave_types.components.create')
        @include('backEnd.partials.deleteModalAjaxRequest',['item_name' => 'Leave Type'])

        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('leave.Leave Type') }} {{ __('common.List')  }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table">
                            <div id="item_list_tbl">
                                @include('leave::leave_types.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'leave_type_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('common.Status') }}','status'],['4','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['leave_type_list']) }}">

    </section>
@endsection


@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script>
    var baseUrl = $('#app_base_url').val();

    $(document).ready(function () {

        $('#item_create_form').on('submit', function (event) {
            event.preventDefault();
            let formData = new FormData(this);
            let url = '';

            if (formData.get('id') == '')
            {
                url = "{{ route('leave_types.store')}}";
            }
            else{
                url = "{{ route('leave_types.update')}}";
            }

            formData.append('_token', "{{ csrf_token() }}");
            $.ajax({
                url: url,
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.success)
                    {
                        resetAfterChange()
                        toastr.success(response.success)
                        $('#item_add').modal('hide');
                    }
                    else{
                        toastr.error(response.error);
                    }
                },
                error: function (response) {
                    showValidationErrors('.item_create_form', response.responseJSON.errors);
                }
            });
        });
        $('#deleteItemModal').on('submit', function (event) {
            event.preventDefault();
            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id', $('#delete_item_id').val());
            $.ajax({
                url: "{{ route('leave_types.delete')}}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.success)
                    {
                        resetAfterChange();
                        toastr.success(response.success);
                        $('#deleteItemModal').modal('hide');
                    }
                    else{
                        toastr.error(response.error);
                    }
                },
            });
        });

    });

    function createModalShow() {
        $('#item_add').modal('show');
        $('.modal-title span').text('{{ trans('common.Add New') }}');
        resetForm();
    }

    function showValidationErrors(formType, errors) {
        $(formType + ' #name_error').text(errors.name);
    }

    function showDeleteModal(imteId) {
        $('#delete_item_id').val(imteId);
        $('#deleteItemModal').modal('show');
    }

    function editItem(item) {
        $('#item_add').modal('show');
        $('.modal-title span').text('{{ trans('common.Edit') }}');
        $('#item_id').val(item.id);
        $("#name").val(item.name);
        if (item.status == 1) {
            $('#status_active').prop("checked", true);
            $('#status_inactive').prop("checked", false);
        } else {
            $('#status_active').prop("checked", false);
            $('#status_inactive').prop("checked", true);
        }
    }

    function resetForm() {
        $('form')[0].reset();
        $('#name_error').text('');
        $('#name').val('');
        $('#status_active').prop("checked", true);
    }

    function resetAfterChange() {
        $.ajax({
            url: "{{route("leave_types.index")}}",
            type: "GET",
            dataType: "HTML",
            success: function (response) {
                $("#item_list_tbl").html(response);
                $('.select_div').niceSelect();
            },
            error: function (error) {
                console.log(error);
            }
        });
        resetForm();
    }

</script>
@endpush
