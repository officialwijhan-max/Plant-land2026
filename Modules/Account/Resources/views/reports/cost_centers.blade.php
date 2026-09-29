@extends('backEnd.master', ['title' => 'Cost Centers'])
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Cost Centers') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="white-box">
                        <form method="GET" action="{{ route('cost_centers.index') }}">
                            <div class="row">
                                <div class="col-lg-5 mt-30-md">
                                    <input class="primary-input form-control" type="date" name="from_date" value="{{ $from }}" placeholder="{{ __('common.Date From') }}">
                                </div>
                                <div class="col-lg-5 mt-30-md">
                                    <input class="primary-input form-control" type="date" name="to_date" value="{{ $to }}" placeholder="{{ __('common.Date To') }}">
                                </div>
                                <div class="col-lg-2 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>{{ __('common.Search') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-12 mt-4">
                    @if ($accounts->isEmpty())
                        <div class="alert alert-info">
                            {{ __('account.No accounts are flagged as cost centers yet.') }}
                            {{ __('account.Set is_cost_center = 1 on a Chart of Accounts row to include it here.') }}
                        </div>
                    @else
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="table-responsive">
                                <table class="table Crm_table_active3">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('account.Code') }}</th>
                                            <th scope="col">{{ __('account.Cost Center') }}</th>
                                            <th scope="col">{{ __('account.Debit') }}</th>
                                            <th scope="col">{{ __('account.Credit') }}</th>
                                            <th scope="col">{{ __('account.Net') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accounts as $account)
                                            <tr>
                                                <td>{{ $account->code }}</td>
                                                <td>{{ $account->name }}</td>
                                                <td>{{ single_price($account->debit) }}</td>
                                                <td>{{ single_price($account->credit) }}</td>
                                                <td>{{ single_price($account->debit - $account->credit) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
