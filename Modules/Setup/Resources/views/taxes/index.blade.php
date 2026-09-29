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
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('setup.Tax List') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('setup::taxes.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'tax_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{ __('common.ID') }}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('common.Description') }}','description'],['4','{{ __('setup.Rate') }}','rate'],['5','{{ __('common.Status') }}','status'],['6','{{ __('common.Action') }}']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self', ['tax_list']) }}">

    </section>

@include('setup::taxes.create')
@include('setup::taxes.edit')
@include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script type="text/javascript">

        var baseUrl = $('#app_base_url').val();


        function resetAddForm()
        {
            document.getElementById("tax_addForm").reset();
        }

        $("#tax_addForm").on("submit", function (event) {
            event.preventDefault();
            let formData = $(this).serializeArray();
            $.each(formData, function (key, message) {
                $("#" + formData[key].name + "_error").html("");
            });
            $.ajax({
                url: "{{route("tax.store")}}",
                data: formData,
                type: "POST",
                success: function (response) {
                    $("#Tax_Add").modal("hide");
                    document.getElementById("tax_addForm").reset();
                    toastr.success(response.message, trans('js.Success'));
                    window.location.reload();
                },
                error: function (error) {
                    toastr.error(trans('js.Something went wrong', trans('js.Error')));
                    if (error) {
                        $.each(error.responseJSON.errors, function (key, message) {
                            $("#" + key + "_error").html(message[0]);
                        });
                    }
                }
            });
        });

        $(".edit_tax").on("click", function (event) {
            event.preventDefault();
            let id = $(this).data("value");
            $.ajax({
                url: baseUrl + "/setup/tax/" + id + "/edit",
                type: "GET",
                success: function (response) {
                    $(".edit_id").val(response.id);
                    $(".name").val(response.name);
                    $(".rate").val(response.rate);
                    $(".description").val(response.description);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });

        $(document).on("submit", "#tax_EditForm", function (event) {
            event.preventDefault();
            let id = $(".edit_id").val();
            let formData = $(this).serializeArray();
            $.each(formData, function (key, message) {
                $("#edit_" + formData[key].name + "_error").html("");
            });
            $.ajax({
                url: baseUrl + "/setup/tax/update/" + id,
                data: formData,
                type: "POST",
                dataType: "JSON",
                success: function (response) {
                    $("#Tax_Edit").modal("hide");
                    document.getElementById("tax_EditForm").reset();
                    toastr.success(response.message,trans('js.Success'));
                    window.location.reload();
                },
                error: function (error) {
                    toastr.error(trans('js.Something went wrong', trans('js.Error')));
                    if (error) {
                        $.each(error.responseJSON.errors, function (key, message) {
                            $("#edit_" + key + "_error").html(message[0]);
                        });
                    }
                }
            });
        });

        function update_active_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('tax.update_active_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    toastr.success(trans('js.Updated Successfully'),trans('js.Success'));
                }
                else{
                    toastr.error(trans('js.Something went wrong', trans('js.Error')));
                }
            });
        }
    </script>
@endpush
