<div class="row form below_div d-none below_div_select below_acc_div">
    <div class="col-lg-3 below_acc_div">
        <div class="primary_input mb-15">
            <label class="primary_input_label" for="">{{trans('account.select_account')}} *</label>
            <select class="select2 mb-15 payment_to_main" name="debit_account_id[]" required>
                <option value="0">{{trans('account.Select one')}}</option>
            </select>
            <span class="text-danger">{{$errors->first('debit_account_id.*')}}</span>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="primary_input mb-15">
            <label class="primary_input_label" for="">{{trans('account.select_sub_account')}}</label>
            <div class="sub_account_below_div">
                <select class="select2 mb-15 payment_to_sub" name="debit_sub_account_id[]">
                    <option value="0">{{trans('account.Select one')}}</option>
                </select>
            </div>
            <span class="text-danger">{{$errors->first('debit_sub_account_id')}}</span>
        </div>
    </div>

    <div class="col-lg-2">
        <div class="primary_input mb-15">
            <label class="primary_input_label" for=""> {{trans('account.amount')}} *</label>
            <input class="primary_input_field sub_amount" name="sub_amount[]" id="sub_amount" value="0" placeholder="Amount" type="number">
            <span class="text-danger">{{$errors->first('sub_amount.*')}}</span>
        </div>
    </div>

    <div class="col-lg-1">
        <div class="primary_input mb-15 action_div">
            <label class="primary_input_label" for=""> {{trans('account.action')}} </label>
            <a class="primary-btn btn-sm delete_payment_to_form"><i class="fas fa-trash-alt required_mark2 f_s_13"></i></a>
        </div>
    </div>
</div>
