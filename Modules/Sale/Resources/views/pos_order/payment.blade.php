@extends('backEnd.master')
@section('mainContent')
    <div id="add_product">
        <section class="admin-visitor-area up_st_admin_visitor">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{__('sale::sale.payment')}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <form action="{{route("pos-order.update",$pos->id)}}" method="POST" id="post_form_submit_multiple" class="payment_form"
                              enctype="multipart/form-data">@method('PUT')
                            @csrf
                        <div class="white_box_50px box_shadow_white">
                            <div class="row">
                                <div class="col-md-9 col-lg-9 col-sm-12">
                                    <!-- Prefix  -->
                                        <div class="pos_payment_method mt-20">
                                            <div class="row">
                                                <div class="col-md-6 col-lg-6 col-sm-12">
                                                    <div class="primary_input mb-15">
                                                        <label class="primary_input_label" for="">{{trans('common.Amount')}}</label>
                                                        <input type="text" name="amount[]" class="primary_input_field"
                                                               placeholder="Enter Amount">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-6 col-sm-12">
                                                    <div class="primary_input mb-15">
                                                        <label class="primary_input_label" for="">{{trans('sale.Payment Method')}}</label>
                                                        <select class="primary_select mb-15 payment_method" id="payment_method"
                                                                name="payment_method[]">
                                                            <option selected disabled>{{trans('common.Select')}}</option>
                                                            <option value="cash-00">{{trans('account.cash')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" class="quick_amounts" name="quick_amounts">

                                    <div class="row justify-content-center mt-20">
                                        <a href="javascript:void(0)" class="primary-btn fix-gr-bg btn-copy"><i
                                                class="ti-plus"></i>
                                            {{__('sale::sale.add_new_payment')}}</a>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3 col-sm-12">
                                    <ul class="quick_cash">
                                        <li><a class="add_cash" data-id="5000" href="javascript:void(0)">5000 <span class="quick_cash_number"></span></a></li>
                                        <li><a class="add_cash" data-id="1000" href="javascript:void(0)">1000 <span class="quick_cash_number"></span></a></li>
                                        <li><a class="add_cash" data-id="500" href="javascript:void(0)">500 <span class="quick_cash_number"></span></a></li>
                                        <li><a class="add_cash" data-id="100" href="javascript:void(0)">100 <span class="quick_cash_number"></span></a></li>
                                        <li><a class="add_cash" data-id="50" href="javascript:void(0)">50 <span class="quick_cash_number"></span></a></li>
                                        <li><a class="add_cash" data-id="20" href="javascript:void(0)">20 <span class="quick_cash_number"></span></a></li>
                                        <li><a class="add_cash" data-id="10" href="javascript:void(0)">10 <span class="quick_cash_number"></span></a></li>
                                        <li class="clear_quick_cash"><a href="javascript:void(0)">{{trans('sale.Clear')}}</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-md-2 col-lg-2 col-sm-4">
                                    <h6> {{trans('common.Total Qty')}} : {{$pos->total_quantity}}</h6>
                                </div>
                                <div class="col-md-2 col-lg-2 col-sm-4">
                                    <h6> {{__('sale::sale.total_payable')}} : ${{$pos->payable_amount}}</h6>
                                </div>
                                <div class="col-md-3 col-lg-3 col-sm-4">
                                    <button type="submit" class="primary-btn semi-large fix-gr-bg multiple-payment-button">
                                        {{trans('account.finalize_payment')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function () {

                $(document).on('click', '.multiple-payment-button', function (e) {
                    e.preventDefault()
                    $('.multiple-payment-button').prop('disabled', true);
                    totalCalcuationAfterChange();
                    $("#post_form_submit_multiple").submit();
                });

                let i = 0;
                let amounts = [];
                $('.quick_cash_number').hide()
                $(".btn-copy").on('click', function () {
                    i += 1;
                    let main_div = '<div class="pos_payment_method mt-20">';
                    let row = '<div class="row">';
                    let cols = '<div class="col-md-6 col-lg-6 col-sm-12">';
                    let input_div = '<div class="primary_input mb-15">';
                    let label = '<label class="primary_input_label" for="">Amount</label>';
                    let label2 = ' <label class="primary_input_label" for="">Payment Method</label>';
                    let input_field = '<input type="text" name="amount[]" class="primary_input_field" placeholder="Enter Amount"></div></div>';
                    let select = '<select class="primary_select mb-15 payment_method" name="payment_method[]"><option selected disabled>Select</option><option value="cash">Cash</option><option value="bank">Bank</option></select></div></div></div></div>';
                    if($('.pos_payment_method').length == 1)
                        $('.pos_payment_method').after(main_div + row + cols + input_div + label + input_field + cols + label2 + select);
                    else
                        $('.pos_payment_method').last().after(main_div + row + cols + input_div + label + input_field + cols + label2 + select);

                    $('select').niceSelect(); // add this
                })
                $(document).on('change', '.payment_method', function () {
                    let row = '<div class="row bank_info appended_inputs">';
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

                    if ($(this).val().split('-')[0] == 'bank')
                        $(this).closest($('.pos_payment_method'))
                            .append(row + cols + input_div + label + bank_name + cols + input_div + label2 + branch + end_row + row + cols + input_div + label3 + account_no + cols + input_div + label4 + owner + end_row)
                    else {
                        // $(this).closest($('.pos_payment_method')).children($('.appended_inputs')).remove();
                        $(this).parent().parent().parent().find('.bank_info').remove();
                    }
                });

                $(document).on('click','.add_cash',function (){
                    let selector = $(this).children('.quick_cash_number');
                    let value = selector.text();
                    let amount = $(this).data('id');
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
                        $('.quick_amounts').val(amounts)
                    }
                })

                $(document).on('click','.clear_quick_cash',function (){
                    amounts = [];
                    $('.quick_amounts').val('');
                    let selector = $('.quick_cash_number');
                    selector.hide();
                    selector.text('');
                })
            })
        </script>
    @endpush
@endsection
