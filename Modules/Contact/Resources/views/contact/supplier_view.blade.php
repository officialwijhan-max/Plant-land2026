@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{ __('common.Supplier') }} {{__('common.Profile')}}</h3>
                            </div>

                            <ul class="d-flex">
                                <li><a class="primary-btn radius_30px mr-10 fix-gr-bg"
                                       href="{{route('add_contact.edit',$supplier->id)}}"><i
                                            class="ti-pen"></i>{{ __('common.Edit') }}</a></li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-lg-5 col-sm-12">
                                <img class="student-meta-img img-100 mb-3"
                                     src="{{ file_exists(@$supplier->avatar) ? asset(@$supplier->avatar) : asset('public/img/profile.jpg') }}"
                                     alt="">
                                <h3>{{$supplier->name}}</h3>
                                <table class="table table-borderless supplier_view">
                                    <tr>
                                        <td>{{ __('common.Name') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $supplier->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Email') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $supplier->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Phone') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $supplier->mobile }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Pay Term') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $supplier->pay_term }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Pay Condition') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $supplier->pay_term_condition }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Address') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $supplier->address }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('contact.Country') }}</td>
                                        <td>: <span class="ml-1"></span>{{ @$supplier->country->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('contact.State') }}:</td>
                                        <td>: <span class="ml-1"></span>{{ @$supplier->state->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('contact.City') }}:</td>
                                        <td>: <span class="ml-1"></span>{{ @$supplier->city->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Tax Number') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $supplier->tax_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Opening Balance') }}</td>
                                        <td>: <span class="ml-1"></span>{{ single_price($supplier->opening_balance) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Registered Date') }}</td>
                                        <td>: <span
                                                class="ml-1"></span>{{ showDate($supplier->created_at) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Active Status') }}</td>
                                        <td>: <span class="ml-1"></span>
                                            @if ($supplier->is_active == 1)
                                                <span class="badge_1">{{__('common.Active')}}</span>
                                            @else
                                                <span class="badge_4">{{__('common.DeActive')}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-2 col-lg-2 col-sm-12 supplier_profile">
                                <h3>{{__('purchase.Purchase Information')}}</h3>
                                <table class="table table-borderless supplier_view">
                                    <tr>
                                        <td>{{__('purchase.Total Invoice')}}
                                            : {{$supplier->accounts['total_invoice']}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('purchase.Due Invoice')}} : {{$supplier->accounts['due_invoice']}}</td>
                                    </tr>
                                </table>
                                <a href="{{ route('supplierPurchaseProductList', $supplier->id) }}"
                                   class="primary-btn radius_30px mr-10 fix-gr-bg"><i
                                        class="fa fa-bars"></i> {{__('product.Products')}}</a>
                            </div>
                            <div class="col-md-1 col-lg-1 col-sm-12"></div>
                            <div class="col-md-4 col-lg-4 col-sm-12 supplier_profile">
                                <h3>{{__('sale.Finance Information')}}</h3>
                                <table class="table table-borderless supplier_view">
                                    @if ($supplier->accounts)
                                        <tr>
                                            <td>{{__('purchase.Total Purchase')}}
                                                : {{single_price($supplier->accounts['total'])}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('purchase.Due Balance')}}
                                                : {{single_price($supplier->accounts['due'])}}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><small>['{{__('purchase.Total Purchase = Purchase + Opening Balance')}}
                                                ']</small></td>
                                    </tr>
                                </table>
                                <a data-toggle="modal" data-target="#addBalanceModal"
                                   class="primary-btn radius_30px mr-10 fix-gr-bg"><i
                                        class="fa fa-bars"></i>{{__('purchase.Add Balance')}}</a>
                                <a data-toggle="modal" data-target="#substractBalanceModal"
                                   class="primary-btn radius_30px mr-10 fix-gr-bg"><i
                                        class="fa fa-minus"></i>{{__('purchase.Subtract Balance')}}</a>
                                @if (permissionCheck('openning_balance.create'))
                                    <a href="{{ route('openning_balance.create') }}"
                                       class="primary-btn radius_30px mr-10 fix-gr-bg"><i
                                            class="fa fa-book"></i>{{__('account.Opening Balance')}}</a>
                                @endif
                            </div>
                        </div>
                        <hr>
                        @if ($supplier->note != null)
                            <div class="row">
                                <div class="col">
                                    <label class="primary_input_label" for="">
                                        @php
                                            echo $supplier->note
                                        @endphp
                                    </label>
                                </div>
                            </div>
                            <hr>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="col-lg-12 student-details">
                            <ul class="nav nav-tabs tab_column mb-50 border-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#Invoice" role="tab"
                                       data-toggle="tab">{{ __('common.Invoice') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#Return" role="tab"
                                       data-toggle="tab">{{ __('common.Return') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="#Transactions" role="tab"
                                       data-toggle="tab">{{__('common.Transactions')}}</a>
                                </li>
                            </ul>
                            <div class="tab-content pt-30">

                                <div role="tabpanel" class="tab-pane fade show active" id="Invoice">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="QA_section QA_section_heading_custom check_box_table">
                                                <div class="QA_table ">
                                                    <div id="item_list_tbl">
                                                        @include('contact::contact.paginates.supplier_invoice_list')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="Return">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="QA_section QA_section_heading_custom check_box_table">
                                                <div class="QA_table ">
                                                    <div id="item_list_tbl_2">
                                                        @include('contact::contact.paginates.supplier_return_invoice_list')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane fade" id="Transactions">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="QA_section QA_section_heading_custom check_box_table">
                                                <div class="QA_table ">
                                                    <!-- table-responsive -->
                                                    @if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro")
                                                        @include('contact::contact.credit_transaction_list_table')
                                                    @else
                                                        @include('contact::contact.credit_transaction_list_table', ['chartAccount' => Modules\Account\Entities\ChartAccount::where('contactable_type', 'Modules\Contact\Entities\ContactModel')->where('contactable_id', $supplier->id)->first()])
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="Voucher_info"></div>
    <div id="getDetails"></div>
    <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    <input type="hidden" name="account_select_list_option" id="account_select_list_option" value="{{ (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") ? route('leadger.get_leadger_for_select') : route('account.select_all_list') }}">
    <input type="hidden" name="expense_account_select_list_option" id="expense_account_select_list_option" data-type="3" value="{{ route('account.select_based_on_type') }}">
    <input type="hidden" name="select_based_on_configuration_group" id="select_based_on_configuration_group" value="{{ (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") ? route('pro_account.cash_bank_account_select') : route('account.select_based_on_configuration_group') }}">
    @include('contact::contact.minus_balance_modal_supplier')
    @include('contact::contact.add_balance_modal_supplier')
@endsection
@push("scripts")
<script src="{{ Module::asset('tables:contact_view_table_tab.js') }}"></script>
<script src="{{ Module::asset('core:all_accounts.js') }}"></script>
<script src="{{ Module::asset('core:expense_accounts.js') }}"></script>
    <script type="text/javascript">
        function getDetails(el) {
            "use strict"
            $.post('{{ route('get_purchase_details') }}', {_token: '{{ csrf_token() }}', id: el}, function (data) {
                $('#getDetails').html(data);
                $('#purchase_info_modal').modal('show');
                $('select').niceSelect();
            });
        }

        function get_accounts() {
            "use strict"
            var account_cat_id = $('#payment_from_type').val();
            if (account_cat_id == 1) {
                $("#bank_branch").attr('disabled', true).attr('required', false);
                $("#bank_name").attr('disabled', true).attr('required', false);
                $("#cheque_date").attr('disabled', true).attr('required', false);
                $("#cheque_no").attr('disabled', true).attr('required', false);
                $(".bank_branch_div").hide();
                $(".bank_name_div").hide();
                $(".cheque_date_div").hide();
                $(".cheque_no_div").hide();
            } else if (account_cat_id == 2) {
                $("#bank_branch").removeAttr("disabled").attr('required', true);
                $("#bank_name").removeAttr("disabled").attr('required', true);
                $("#cheque_date").removeAttr("disabled").attr('required', true);
                $("#cheque_no").removeAttr("disabled").attr('required', true);
                $(".bank_branch_div").show();
                $(".bank_name_div").show();
                $(".cheque_date_div").show();
                $(".cheque_no_div").show();
            }
        }

        (function ($) {
            "use strict";
            $(document).ready(function () {
                $('.payment_from_div').hide();
                $(".bank_branch_div").hide();
                $(".bank_name_div").hide();
                $(".cheque_date_div").hide();
                $(".cheque_no_div").hide();
            });
        })(jQuery);
    </script>
@endpush
