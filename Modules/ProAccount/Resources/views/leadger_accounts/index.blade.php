@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings("site_title") .' | '. trans('account.chart_of_accounts'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.chart_of_accounts'). '/' .trans('account.leadger_account') }}</h3>
                            <ul class="d-flex">
                                @if (permissionCheck('leadger.store'))
                                    <li><a class="primary-btn radius_30px mr-10 fix-gr-bg create_modal" href="#"  data-toggle="modal" data-target="#Item_Details"><i class="ti-plus"></i>{{ trans('account.add_new_leadger') }}</a></li>
                                @endif
                                {{-- @if (permissionCheck('leadger.import_page'))
                                    <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{ route('leadger.import_page') }}"><i class="ti-plus"></i>{{ trans('account.import_leadger_accounts') }}</a></li>
                                @endif --}}
                                @if (permissionCheck('leadger.export_csv'))
                                    <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{ route('leadger.export_csv') }}" target="_blank"><i class="ti-download"></i>{{ trans('account.export_leadger_accounts') }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div class="table-responsive">
                                <div class="" id="chart_account_list">
                                    @include('proaccount::leadger_accounts.page_component.ledger_list', ['ChartOfAccountList' => $ChartOfAccountList])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" id="edit_url" name="edit_url" value="{{ route('leadger.edit',':id') }}">
    <div id="edit_form"></div>
    @include('proaccount::leadger_accounts.create')
    {{-- @include('proaccount::leadger_accounts.page_component.rename_model') --}}
    @include('proaccount::modals._deleteModalForAjax',['item_name' => trans('account.leadger_account')])
@endsection
@push('scripts')
    <script src="{{ Module::asset('account:leadger.js') }}"></script>
@endpush
