@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings("site_title") .' | '. trans('account.ledger_reports'))
@push('styles')
    <style>
        hr {
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
            border: 0;
            border-top: 1px solid rgba(0,0,0,.1);
        }
    </style>
@endpush
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.banking_transaction') }} : {{ $statement->leadger->name }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table">
                            <div class="table-responsive">
                            <table class="table" id="leadgerTbl">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ trans('account.date') }}</th>
                                        <th scope="col">{{trans('account.bank_detail')}}</th>
                                        <th scope="col">{{ trans('account.matched') }}</th>
                                        <th scope="col">{{ trans('account.spent') }}</th>
                                        <th scope="col">{{ trans('account.recieved') }}</th>
                                        <th scope="col">{{ trans('account.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($statement->banking_statement_details as $detail)
                                        <tr>
                                            <td>{{ showDate($detail->date) }}</td>
                                            <td>{{ $detail->narration }}</td>
                                            <td>
                                                <ul class="permission_list mt-3">
                                                    @foreach ($detail->reconciled_amounts as $item)
                                                        <li class="mb-2">
                                                            <a href="{{route('journal.transaction_detail',$item->voucher->id)}}" target="_blank">
                                                                <p>
                                                                    {{ $item->type. " - " }}
                                                                    {{ $item->narration ? $item->narration : $item->voucher->narration }} <br>
                                                                </p>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>
                                                @if (($statement->leadger->type == 1 || $statement->leadger->type == 3) && $detail->sign == 0)
                                                    {{ single_price(abs($detail->amount)) }}
                                                @endif
                                                @if (($statement->leadger->type == 2 || $statement->leadger->type == 4) && $detail->sign == 1)
                                                    {{ single_price(abs($detail->amount)) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (($statement->leadger->type == 1 || $statement->leadger->type == 3) && $detail->sign == 1)
                                                    {{ single_price(abs($detail->amount)) }}
                                                @endif
                                                @if (($statement->leadger->type == 2 || $statement->leadger->type == 4) && $detail->sign == 0)
                                                    {{ single_price(abs($detail->amount)) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($detail->is_approve == 0 && $detail->is_matched == 1)
                                                    <div class="btn_div">
                                                        @if (permissionCheck('banking_statement.approve_reconcile'))
                                                            <a class="primary-btn radius_30px mr-10 approve-btn fix-gr-bg" data-id='{{ $detail->id }}'><i class="fa fa-check"></i></a>
                                                        @endif
                                                        @if (permissionCheck('banking_statement.undo_reconcile'))
                                                            <a class="primary-btn radius_30px mr-10 undo-btn fix-gr-bg" data-id='{{ $detail->id }}'><i class="fa fa-undo"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if ($detail->is_matched == 0)
                                                    @if (permissionCheck('banking_statement.show'))
                                                        <a class="primary-btn radius_30px mr-10 undo-btn fix-gr-bg" href="{{ route('banking_statement.show',$statement->id) }}"><i class="fa fa-eye"></i>{{ trans('common.Pending') }}</a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push("scripts")
<script>
    $(document).on('click', '.undo-btn', function(){
        let btn = $(this);
        let tr = $(this).closest("tr");
        let _id = $(this).attr("data-id");
        let _token = $('meta[name=_token]').attr('content');
        let formData = new FormData();
        formData.append('_token',_token);
        formData.append('id',_id);
        $.ajax({
            url: '{{ route('banking_statement.undo_reconcile') }}',
            type:"POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success:function(response){
                tr.remove();
                toastr.success(response.message);
            },
            error:function(response) {
                toastr.error(response.message);
            }
        });
    });
    $(document).on('click', '.approve-btn', function(){
        let btn = $(this);
        let tr = $(this).parent("div");
        let _id = $(this).attr("data-id");
        let _token = $('meta[name=_token]').attr('content');
        let formData = new FormData();
        formData.append('_token',_token);
        formData.append('id',_id);
        $.ajax({
            url: '{{ route('banking_statement.approve_reconcile') }}',
            type:"POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success:function(response){
                tr.remove();
                toastr.success(response.message);
            },
            error:function(response) {
                toastr.error(response.message);
            }
        });
    });
</script>
@endpush
