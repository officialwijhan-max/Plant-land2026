@extends('backEnd.master')
@section('mainContent')
    @include("backEnd.partials.alertMessage")
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-visitor-area up_st_admin_visitor">

        @include('setup::department.components.create')
        @include('setup::department.components.edit')
        @include('backEnd.partials.deleteModalAjaxRequest',['item_name' => 'Department'])

        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('department.Department') }} {{ __('common.List')  }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table">
                            <div class="" id="item_list_tbl">
                                {{-- Department List --}}
                                @include('setup::department.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    @php
    $employee_per = auth()->user()->user_col_permissions->where('table_name', 'department_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{ __('common.ID') }}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('role.Details') }}','details'],['4','{{ __('common.Status') }}','status'],['5','{{ __('common.Action') }}']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self', ['department_list']) }}">


@endsection


@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>

<script>
    var baseUrl = $('#app_base_url').val();

    $(document).ready(function() {
        $('#item_create_form').on('submit',function(event){
            event.preventDefault();
            var formElement = $(this).serializeArray()
            var formData = new FormData();
            formElement.forEach(element => {
                formData.append(element.name,element.value);
            });
            formData.append('_token',"{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('departments.store')}}",
                type:"POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    resetAfterChange()
                    toastr.success(response.message, trans('js.Success'))
                    $('#item_add').modal('hide');
                },
                error:function(response) {
                    showValidationErrors('.item_create_form',response.responseJSON.errors);
                }
            });
        });

        $('#item_edit_form').on('submit',function(event){
            event.preventDefault();
            var formElement = $(this).serializeArray()
            var formData = new FormData();
            formElement.forEach(element => {
                formData.append(element.name,element.value);
            });
            formData.append('_token',"{{ csrf_token() }}");
            formData.append('id',$('#item_id').val());
            $.ajax({
                url: "{{ route('departments.update')}}",
                type:"POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    resetAfterChange()
                    toastr.success(response.message, trans('js.Success'))
                    $('#item_edit').modal('hide');
                },
                error: function(response) {
                    showValidationErrors('.item_edit_form',response.responseJSON.errors);
                }
            });
        });

        $('#deleteItemModal').on('submit',function(event){
            event.preventDefault();
            var formData = new FormData();
            formData.append('_token',"{{ csrf_token() }}");
            formData.append('id',$('#delete_item_id').val());
            $.ajax({
                url: "{{ route('departments.delete')}}",
                type:"POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    resetAfterChange();
                    toastr.success(response.message, trans('js.Success'))
                    $('#deleteItemModal').modal('hide');
                },
                error: function(response) {
                    toastr.error('Something wrong !')
                }
            });
        });

    });

    function createModalShow(){
        $('#item_add').modal('show');
        resetForm();
    }

    function showValidationErrors(formType, errors){
        $(formType +' #name_error').text(errors.name);
        $(formType +' #details_error').text(errors.percentage);
        $(formType +' #status_error').text(errors.quantity);
    }

    function showDeleteModal(imteId){
        $('#delete_item_id').val(imteId);
        $('#deleteItemModal').modal('show');
    }

    function editItem(item){
        $('#item_edit').modal('show');
        $('#item_id').val(item.id);
        $(".item_edit_form #name").val(item.name);
        $('.item_edit_form #details').val(item.details);
        if(item.status == 1){
            $('.item_edit_form #status_active').prop("checked", true);
            $('.item_edit_form #status_inactive').prop("checked", false);
        }else{
            $('.item_edit_form #status_active').prop("checked", false);
            $('.item_edit_form #status_inactive').prop("checked", true);
        }
    }

    function resetForm(){
        $('form')[0].reset();
        $('#name_error').text('');
        $('#percentage_error').text('');
    }

    function resetAfterChange() {
        $.ajax({
            url: "{{route("departments.index")}}",
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
    }

</script>
@endpush
