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
        .match_found_color {
            color: blueviolet;
        }
        .reconcile_list{
            display: none;
        }
        .pointer {
            cursor: pointer;
        }
        .select2-container {
            z-index: inherit !important;
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
                                            <th scope="col" width="20%">{{trans('account.bank_detail')}}</th>
                                            <th scope="col" width="35%">{{ trans('account.matched') }}</th>
                                            <th scope="col">{{ trans('account.spent') }}</th>
                                            <th scope="col">{{ trans('account.recieved') }}</th>
                                            <th scope="col" class="text-center" width="10%">{{ trans('account.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($statement->banking_statement_details->where('is_matched', 0) as $detail)
                                            <tr>
                                                <td>{{ showDate($detail->date) }}</td>
                                                <td>{{ $detail->narration }}</td>
                                                <td>
                                                    @if (count($detail->MatcheAmount) > 1)
                                                    <span class="badge_1 mt-2 reconcile_more pointer">{{ __('common.See More') }}</span>
                                                        <div class="reconcile_list">
                                                            <ul class="permission_list mt-3">
                                                                @foreach ($detail->MatcheAmount as $item)
                                                                    <li class="mb-4">
                                                                        <label class="primary_checkbox d-flex mr-12 ">
                                                                            <input name="accounting_entry_system" class="accounting_entry_system" type="radio" id="accounting_entry_system" value="{{ $item->id }}">
                                                                            <span class="checkmark"></span>
                                                                        </label>
                                                                        <a href="{{route('journal.transaction_detail',$item->voucher->id)}}" target="_blank">
                                                                            <p>
                                                                                {{ $item->type. " - " }}
                                                                                {{ $item->narration ? $item->narration : $item->voucher->narration }} <br>
                                                                            </p>
                                                                            @foreach ($item->GetOppositeSideAccount() as $transaction)
                                                                                <p>{{ $transaction->type. " - " }}{{ $transaction->leadger->name }}</p>
                                                                            @endforeach
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                            <div class="btn_div mb-3">
                                                                @if ($detail->is_matched == 0 && count($detail->MatcheAmount) != 0)
                                                                    <button class="primary-btn small tr-bg match-btn" type="button" data-id='{{ $detail->id }}'> {{ __('account.Match') }}</button>
                                                                @endif
                                                                @if (permissionCheck('banking_statement.create_transaction_modal'))
                                                                <button class="primary-btn small tr-bg create-new-btn" type="button" data-id='{{ $detail->id }}'> {{ __('account.Create New') }}</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (count($detail->MatcheAmount) == 0 || count($detail->MatcheAmount) == 1)
                                                        <ul class="permission_list mt-3">
                                                            @foreach ($detail->MatcheAmount as $item)
                                                                <li class="mb-4">
                                                                    <label class="primary_checkbox d-flex mr-12 ">
                                                                        <input name="accounting_entry_system" class="accounting_entry_system" type="radio" id="accounting_entry_system" value="{{ $item->id }}">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                    <a href="{{route('journal.transaction_detail',$item->voucher->id)}}" target="_blank">
                                                                        <p>
                                                                            {{ $item->type. " - " }}
                                                                            {{ $item->narration ? $item->narration : $item->voucher->narration }} <br>
                                                                        </p>
                                                                        @foreach ($item->GetOppositeSideAccount() as $transaction)
                                                                            <p>{{ $transaction->type. " - " }}{{ $transaction->leadger->name }}</p>
                                                                        @endforeach
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        @if ($detail->is_matched == 0 && count($detail->MatcheAmount) != 0)
                                                            <button class="primary-btn small tr-bg match-btn" type="button" data-id='{{ $detail->id }}'> {{ __('account.Match') }}</button>
                                                        @else
                                                            @if (permissionCheck('banking_statement.create_transaction_modal'))
                                                                <button class="primary-btn small tr-bg create-new-btn" type="button" data-id='{{ $detail->id }}'> {{ __('account.Create New') }}</button>
                                                            @endif
                                                        @endif
                                                    @endif
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
                                                <td class="text-right">
                                                    <span @if (count($detail->MatcheAmount) != 0) class="badge_1" @else class="badge_4" @endif>{{ count($detail->MatcheAmount) .' '. __("proaccount::account.match_found") }}</span>
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
    <input type="hidden" value="{{route('sub_leadger.get_data_list_for_select')}}" id="sub_leadger_by_leadger">
    <input type="hidden" value="{{route('leadger.get_leadger_for_select')}}" id="leadger_list_select_option">
    <div id="create_modal_parent_div"></div>
@endsection
@push("scripts")
<script>
    $(document).on('click', '.match-btn', function(){
        $(this).addClass('d-none');
        let transaction_id = $(this).closest("tr").find('ul').children('li').find('.accounting_entry_system:checked').val();
        if (typeof transaction_id !== "undefined") {
            let tr = $(this).closest("tr");
            let _id = $(this).attr("data-id");
            let _token = $('meta[name=_token]').attr('content');
            let formData = new FormData();
            formData.append('_token',_token);
            formData.append('id',_id);
            formData.append('transaction_id',transaction_id);
            $.ajax({
                url: '{{ route('banking_statement.update') }}',
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
                    $(this).removeClass('d-none');
                }
            });
        }else{
            toastr.warning('Check the Check box First !');
            $(this).removeClass('d-none');
        }
    });
    $(document).on('click', '.reconcile_more', function(e){
        $(this).closest('td').find('.reconcile_list').fadeToggle();
    });
    $(document).on('click', '.create-new-btn', function(){
        let _id = $(this).attr("data-id");
        let _token = $('meta[name=_token]').attr('content');
        let formData = new FormData();
        formData.append('_token',_token);
        formData.append('id',_id);
        $.ajax({
            url: '{{ route('banking_statement.create_transaction_modal') }}',
            type:"POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success:function(response){
                $('#create_modal_parent_div').html(response);
                $('#create_transaction').modal('show');
                $('.primary_select').niceSelect();
                getMainAccount();
                getSubAccount();
            },
            error:function(response) {
                toastr.error(response.message);
                $(this).removeClass('d-none');
            }
        });
    });

    function getSubAccount(){
        $(".sub_account_id").select2({
            ajax: {
                url: $('#sub_leadger_by_leadger').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            leadger_id: 0
                        }
                        return query;
                },
                cache: false
            },
            escapeMarkup: function (m) {
                return m;
            }
        });
    }
    function getMainAccount(){
        $(".account_id").select2({
            ajax: {
                url: $('#leadger_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            type: $('.journal_type').find(':selected').val()
                        }
                        return query;
                },
                cache: false
            },
            escapeMarkup: function (m) {
                return m;
            }
        });
    }
</script>
@endpush
