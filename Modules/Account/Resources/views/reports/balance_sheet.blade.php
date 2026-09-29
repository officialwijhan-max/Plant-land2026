@extends('backEnd.master', ['title' => 'Balance Sheet'])
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Balance Sheet') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    @if (round($totalAssets, 2) !== round($totalLiabilities + $totalEquity, 2))
                        <div class="alert alert-danger">
                            {{ __('account.Assets do not equal Liabilities plus Equity') }} ({{ single_price($totalAssets) }} {{ __('account.vs') }} {{ single_price($totalLiabilities + $totalEquity) }}) - {{ __('account.this indicates an unbalanced posting somewhere and should be investigated.') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="QA_section QA_section_heading_custom check_box_table">
                                <div class="box_header common_table_header">
                                    <h4 class="mb-0">{{ __('account.Assets') }}</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table Crm_table_active3">
                                        <tbody>
                                            @foreach ($assets as $account)
                                                <tr>
                                                    <td>{{ $account->name }}</td>
                                                    <td class="text-right">{{ single_price($account->amount) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>{{ __('account.Total Assets') }}</th>
                                                <th class="text-right">{{ single_price($totalAssets) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="QA_section QA_section_heading_custom check_box_table">
                                <div class="box_header common_table_header">
                                    <h4 class="mb-0">{{ __('account.Liabilities') }}</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table Crm_table_active3">
                                        <tbody>
                                            @foreach ($liabilities as $account)
                                                <tr>
                                                    <td>{{ $account->name }}</td>
                                                    <td class="text-right">{{ single_price($account->amount) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>{{ __('account.Total Liabilities') }}</th>
                                                <th class="text-right">{{ single_price($totalLiabilities) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="QA_section QA_section_heading_custom check_box_table mt-4">
                                <div class="box_header common_table_header">
                                    <h4 class="mb-0">{{ __('account.Equity') }}</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table Crm_table_active3">
                                        <tbody>
                                            @foreach ($equity as $account)
                                                <tr>
                                                    <td>{{ $account->name }}</td>
                                                    <td class="text-right">{{ single_price($account->amount) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>{{ __('account.Net Profit (current period)') }}</td>
                                                <td class="text-right">{{ single_price($netProfit) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>{{ __('account.Total Equity') }}</th>
                                                <th class="text-right">{{ single_price($totalEquity) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
