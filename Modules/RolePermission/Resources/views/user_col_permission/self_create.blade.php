<div class="modal fade admin-query" id="hide_show_modal_for_self">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{trans('common.Hide or Show') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('user_column_permission.store_self') }}" method="post">
                    @csrf
                    <input type="hidden" id="employee_id" name="employee_id" value="{{ $employee_id }}">
                    <input type="hidden" id="table_name" name="table_name" value="{{ $table_name }}">
                    <input type="hidden" id="employee_role_id" name="employee_role_id" value="{{ $employee_role_id }}">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table_modal_upper table_modal table-bordered">
                                <thead>
                                    <tr>
                                        <th>Field Name</th>
                                        <th>Visibility</th>
                                    </tr>
                                </thead>
                                <tbody class="dynamically_append_tr">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center">
                                <button class="primary-btn semi_large2  fix-gr-bg mr-10"  type="submit"><i class="ti-check"></i>{{__('common.Save') }}</button>
                                <button class="primary-btn semi_large2  fix-gr-bg" id="save_button_parent" data-dismiss="modal" type="button"><i class="ti-check"></i>{{__('account.Cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
