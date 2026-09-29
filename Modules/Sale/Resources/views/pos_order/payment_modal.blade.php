<div class="modal fade admin-query" id="Pos_Payment_Multiple">
    <div class="modal-dialog modal_1000px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{__('sale.Payment')}}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if ($paid_amount && $paid_amount > 0)
                        <div class="col-12">
                            <h4>{{__('sale.Paid Amount')}} : <span class="paid_amount">{{$paid_amount}}</span></h4>
                        </div>
                    @endif
                    <input type="hidden" name="user_type" id="user_type" value="{{ $user}}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ $user_id}}">
                    <input type="hidden" value="{{$paid_amount ?? 0}}" class="paid_amount">
                    <div class="col-12">
                        <div class="payment__grid_widget">
                            <div class="pos_payment_method_upper">
                                <div id="pos_payment_method" class="pos_payment_method modal_inner_wrap modal_inner_wrap position-relative p_20 radius_10px pos_border pos_bg mb_20">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6 col-sm-12">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label"
                                                       for="">{{__('pos.Amount')}}</label>
                                                <input type="text" id="amount0" name="amount[]"
                                                       onkeyup="getAmount('0')"
                                                       class="primary_input_field input_amounts cash_payment" value="{{ $paying_amount }}" placeholder="{{trans('common.Amount')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-sm-12">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label"
                                                       for="">{{__('pos.Payment Method')}}</label>
                                                <select class="primary_select mb-15 payment_method" id="payment_method0" name="payment_method[]">
                                                    <option value="cash-00-Cash" {{app('general_setting')->payment_gateway == 1 ? 'selected' : ''}}>{{__('pos.Cash')}}</option>
                                                    @foreach ($bank_accounts as $bank_account)
                                                        <option value="bank-{{ $bank_account->id }}-{{ $bank_account->name }}" {{app('general_setting')->payment_gateway == 2 ? 'selected' : ''}}>{{ $bank_account->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row bank_info_div"
                                         @if(app('general_setting')->payment_gateway != 2 ) style="display: none" @endif>
                                        <div class="col-md-6 col-lg-6 col-sm-12">
                                            <div class="primary_input mb-15"><label class="primary_input_label"
                                                                                    for="">{{__('sale.Bank Name')}}</label><input
                                                    type="text" name="bank_name[]" class="primary_input_field"
                                                    placeholder="Bank Name"></div>
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-sm-12">
                                            <div class="primary_input mb-15"><label class="primary_input_label"
                                                                                    for="">{{__('sale.Branch')}}</label><input
                                                    type="text" name="branch[]" class="primary_input_field"
                                                    placeholder="Branch"></div>
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-sm-12">
                                            <div class="primary_input mb-15"><label class="primary_input_label"
                                                                                    for="">{{__('sale.Account No')}}</label><input
                                                    type="text" name="account_no[]" class="primary_input_field"
                                                    placeholder="Account No"></div>
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-sm-12">
                                            <div class="primary_input mb-15"><label class="primary_input_label"
                                                                                    for="">{{__('sale.Account Owner')}}</label><input
                                                    type="text" name="account_owner[]" class="primary_input_field"
                                                    placeholder="Account Owner"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-20 no-gutters">
                                    <a href="javascript:void(0)" class="primary-btn fix-gr-bg btn-copy w-100"><i
                                            class="ti-plus"></i>{{__('pos.Add New Payment')}}</a>
                                </div>
                            </div>
                            <div class="payment_widget_tiket">
                                <input type="hidden" class="quick_amounts" name="quick_amounts">
                                <h4>{{__('pos.Quick Cash')}}</h4>
                                <ul class="quick_cash">
                                    <li><a class="add_cash" data-id="5000" href="javascript:void(0)">5000 <span
                                                class="quick_cash_number"></span></a></li>
                                    <li><a class="add_cash" data-id="1000" href="javascript:void(0)">1000 <span
                                                class="quick_cash_number"></span></a></li>
                                    <li><a class="add_cash" data-id="500" href="javascript:void(0)">500 <span
                                                class="quick_cash_number"></span></a></li>
                                    <li><a class="add_cash" data-id="100" href="javascript:void(0)">100 <span
                                                class="quick_cash_number"></span></a></li>
                                    <li><a class="add_cash" data-id="50" href="javascript:void(0)">50 <span
                                                class="quick_cash_number"></span></a></li>
                                    <li><a class="add_cash" data-id="20" href="javascript:void(0)">20 <span
                                                class="quick_cash_number"></span></a></li>
                                    <li><a class="add_cash" data-id="10" href="javascript:void(0)">10 <span
                                                class="quick_cash_number"></span></a></li>
                                    <li class="clear_quick_cash"><a
                                            href="javascript:void(0)">{{__('pos.Clear')}}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="total_quick_cash" id="quick_cash" value="0">
                <input type="hidden" name="final_amount" id="final_amount" value="0">
                <input type="hidden" name="final_over_sure_amount" class="final_over_sure_amount" value="0">
                <input type="hidden" name="totall_bill" id="totall_bill" value="{{ $total_amount }}">
                <div class="col-lg-12 col-12 text-center payment_warning" style="display: none">
                    <p class="text-danger">{{__('pos.Input Amount Doesnt Match with Payable Amount')}}</p>
                </div>
                <div class="col-lg-12 text-center pt_15">
                    <div class="total_quantity_buttons">
                        <div class="primary_input">
                            <label class="primary_input_label" for="">{{__('pos.Total Quantity')}}</label>
                            <input class="primary_input_field no_min_width" value="{{$total_qty}}" type="text" readonly>
                        </div>
                        <div class="primary_input">
                            <label class="primary_input_label" for="">{{__('purchase.Advance Amount')}}</label>
                            <input class="primary_input_field total_change_amount no_min_width" name="total_change_amount" value="0" type="text" readonly>
                        </div>
                        <div class="payment_buttons">
                            <button class="primary_color_btn dark_btn">{{single_price($total_amount)}}</button>

                            <button type="submit" class="primary_color_btn payment_btn submit_button_form multiple-payment-button green_btn">{{__('pos.Finalize Payment')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    let i = 0;
    let amounts = [];
    var myarray = [];
    var input_amounts = [0];
    let bank = '{{app('general_setting')->payment_gateway}}'

    $(document).ready(function () {
        $(document).on('click','.submit_button_form',function(){
            $('.submit_button_form').prop('disabled', true);
            $('.submit_button_form').addClass('d-none');
        });
        $(document).on('click', '.multiple-payment-button', function (e) {
            e.preventDefault()
            $('.multiple-payment-button').prop('disabled', false);
            totalCalcuationAfterChange();
            $("#post_form_submit").submit();
        });

        $('.quick_cash_number').hide();
        $(document).on('click', '.btn-copy', function () {
            myarray.push($('#payment_method' + i).val());
            i += 1;
            let close = '<div class="close_payment"><a href="javascript:void(0)" onclick="removeDiv('+i+')" id="close_payment" class="close_payment'+i+'"><i class="fas fa-minus-circle required_mark2 f_s_13"></i></a></div>';
            let main_div = '<div id="pos_payment_method'+i+'" class="pos_payment_method modal_inner_wrap modal_inner_wrap position-relative p_20 radius_10px pos_border pos_bg mb_20">';
            let row = '<div class="row">';
            let cols = '<div class="col-md-6 col-lg-6 col-sm-12">';
            let input_div = '<div class="primary_input mb-15">';
            let label = '<label class="primary_input_label" for="">Amount</label>';
            let label2 = ' <label class="primary_input_label" for="">Payment Method</label>';
            let input_field = '<input type="text" id="amount' + i + '" onkeyup="getAmount(' + i + ')" name="amount[]" class="primary_input_field input_amounts" placeholder="Enter Amount"></div></div>';
            let select = '<select class="primary_select mb-15 payment_method" id="payment_method' + i + '" name="payment_method[]">' +
                '<option selected disabled>Select</option>' +
                '<option value="cash-00-Cash">{{__("pos.Cash")}}</option>'+
                '@foreach($bank_accounts as $bank_account)' +
                '<option value="bank-{{ $bank_account->id }}-{{ $bank_account->name }}">{{ $bank_account->name }}</option>' +
                '@endforeach' +
                '</select>' +
                '</div></div></div></div>';
            if ($('.pos_payment_method').length == 1)
                $('.pos_payment_method').after( main_div + close + row + cols + input_div + label + input_field + cols + label2 + select);
            else
                $('.pos_payment_method').last().after( main_div + close + row + cols + input_div + label + input_field + cols + label2 + select);

            $('select').niceSelect(); // add this
        })
        $(document).on('change', '.payment_method', function () {
            if (jQuery.inArray($(this).val(), myarray) == -1) {
                let div = '<div class="pos_payment_method modal_inner_wrap modal_inner_wrap position-relative p_20 radius_10px pos_border pos_bg mb_20">';
                let row = '<div class="row bank_class">';
                let cols = '<div class="col-md-6 col-lg-6 col-sm-12">';
                let input_div = '<div class="primary_input mb-15">';
                let label = '<label class="primary_input_label" for="">Bank Name</label>';
                let label2 = ' <label class="primary_input_label" for="">Branch</label>';
                let label3 = ' <label class="primary_input_label" for="">Account No</label>';
                let label4 = ' <label class="primary_input_label" for="">Account Owner</label>';
                let bank_name = '<input type="text" name="bank_name[]" class="primary_input_field" placeholder="Bank Name"></div></div>';
                let branch = '<input type="text" name="branch[]" class="primary_input_field" placeholder="Branch"></div></div>';
                let account_no = '<input type="text" name="account_no[]" class="primary_input_field" placeholder="Account No"></div></div>';
                let owner = '<input type="text" name="account_owner[]" class="primary_input_field" placeholder="Account Owner"></div></div>';
                let end_row = '</div>';

                if ($(this).val().split('-')[0] == 'bank' && (bank == 1 || myarray.length >0))
                {
                    if ($('.bank_info_div').is(":visible")) {
                        $('.bank_info_div').remove();
                    }
                    $(this).closest($('.pos_payment_method')).find('.bank_class').remove();
                    $(this).closest($('.pos_payment_method'))
                        .append(row + cols + input_div + label + bank_name + cols + input_div + label2 + branch  + cols + input_div + label3 + account_no + cols + input_div + label4 + owner + end_row)
                } else {
                    if ($(this).val().split('-')[0] == 'cash') {
                        $(this).closest($('.pos_payment_method')).find('.bank_class').remove();
                    }
                    if (bank == 2 && $('.bank_info_div').is(":visible") && $(this).val().split('-')[0] != 'bank'){
                        $('.bank_info_div').remove();
                    }
                    if (bank == 2 && myarray.length == 0 && $(this).val().split('-')[0] == 'bank') {
                        if ($('.bank_info_div').is(":visible")) {
                            $('.bank_info_div').remove();
                        }
                        $(this).closest($('.pos_payment_method')).find('.bank_class').remove();
                        $(this).closest($('.pos_payment_method'))
                            .append(row + cols + input_div + label + bank_name + cols + input_div + label2 + branch  + cols + input_div + label3 + account_no + cols + input_div + label4 + owner + end_row)
                    }
                }
                $('.save_btn').prop('disabled', false);
            } else {
                $(this).closest($('.pos_payment_method')).find('.bank_class').remove();
                toastr.warning('This was selected before. Without Changing this you cannot submit this.');
                $('.save_btn').prop('disabled', true);
            }
        });

        $(document).on('click','.add_cash',function (){
            let selector = $(this).children('.quick_cash_number');
            let value = selector.text();
            let amount = $(this).data('id');
            var total_amount = 0;
            selector.show()

            if (value)
            {
                selector.text(parseInt(value)+1)
                amounts.push(amount);
                $('.quick_amounts').val(amounts);
            }

            else {
                selector.text(1);
                amounts.push(amount);
                $('.quick_amounts').val(amounts);
            }

            for (var i = 0; i < amounts.length; i++) {
                total_amount += parseInt(amounts[i]);
            }
            $('#quick_cash').val((total_amount).toFixed(2));
            $('.cash_payment').val((total_amount).toFixed(2));
            getAmount(i);
        })

        $(document).on('click','.clear_quick_cash',function (){
            amounts = [];
            $('.quick_amounts').val('');
            let selector = $('.quick_cash_number');
            selector.hide();
            selector.text('');
            $('.total_recieved_amount').text('');
            $('.total_change_amount').val(0);
            $('.input_amounts').val(0);
        })
    })

    function removeDiv(i)
    {
        let index = myarray.indexOf($('#payment_method' + i).val());
        if (index > -1) {
            myarray.splice(index, 1);
        }
        $('#pos_payment_method'+i).remove();

        getAmount(i)
    }

    function getAmount(el) {
        let quick = $('#quick_cash').val();
        let total_amount =0;
        $.each($('.input_amounts'), function (index, value) {
            let amount = $(this).val();
            if (!isNaN(amount) && amount > 0 )
                total_amount += parseFloat(amount);
        });
        var total_bill = parseFloat($('#totall_bill').val());
        var paid_amount = parseFloat($('.paid_amount').val());

        let final_amount = ((parseFloat(total_amount) + paid_amount) -parseFloat(total_bill)).toFixed(2);
        var user = $('#user_type').val();
        var user_id = $('#user_id').val();
        $('.final_over_sure_amount').val(final_amount);
        if (final_amount < 0)
        {
            if (user == "customer" && user_id == 1) {
                $('.payment_btn').attr('disabled',true);
                $('.payment_warning').show();
            }
            $('.total_change_amount').val(0);

        }
        else
        {
            if (user == "customer" && user_id == 1) {
                $('.payment_btn').attr('disabled',false);
                $('.payment_warning').hide();
            }
            $('.total_change_amount').val(final_amount);
        }
    }
</script>
