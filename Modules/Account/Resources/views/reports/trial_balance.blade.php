@extends('backEnd.master', ['title' => 'Trial Balance'])
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Trial Balance') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    @if (round($totalDebit, 2) !== round($totalCredit, 2))
                        <div class="alert alert-danger">
                            {{ __('account.Debit and credit totals do not match') }} - {{ __('account.this indicates an unbalanced posting somewhere and should be investigated.') }}
                        </div>
                    @endif

                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table">
                            <div class="table-responsive">
                                <table class="table Crm_table_active3">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('account.Code') }}</th>
                                            <th scope="col">{{ __('account.Account') }}</th>
                                            <th scope="col">{{ __('account.Debit') }}</th>
                                            <th scope="col">{{ __('account.Credit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accounts as $account)
                                            <tr>
                                                <td>{{ $account->code }}</td>
                                                <td>{{ $account->name }}</td>
                                                <td>{{ $account->debit > 0 ? single_price($account->debit) : '' }}</td>
                                                <td>{{ $account->credit > 0 ? single_price($account->credit) : '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">{{ __('common.Total') }}</th>
                                            <th>{{ single_price($totalDebit) }}</th>
                                            <th>{{ single_price($totalCredit) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
