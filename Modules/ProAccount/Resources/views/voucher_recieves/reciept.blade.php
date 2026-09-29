<!DOCTYPE html>
<html>
<head>

    <title>{{trans('account.money_reciept')}} Print</title>

    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/rtl/bootstrap.min.css"/>
    <style>
        .invoice_heading {
            border-bottom: 1px solid black;
            padding: 20px;
            text-transform: capitalize;
            background-color: #e0ebfd;
        }
        body{
            font-family: "Poppins", sans-serif;
        }
        .invoice_logo {
            width: 50%;
            float: left;
            text-align: left;
        }

        .invoice_no {
            text-align: right;
            color: #415094;
        }

        table {
            text-align: left;
            font-family: "Poppins", sans-serif;
            width: 100%;
        }

        td, th {
            color: #828bb2;
            font-size: 16px;
            font-weight: 400;
            font-family: "Poppins", sans-serif;
            padding-bottom: 3px;
        }

        p {
            font-size: 10px;
            color: #454545;
            line-height: 16px;
        }

        .a4_width {
           max-height: 264px;
        }
        .nowrap{
            white-space: nowrap;
        }
        .dashed-underline {
            display: block;
            border-bottom: 1px dashed #000;
            margin: 5px 0;
            width: 100%;
        }
        html{
            height: 100%;
        }
        body{
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .flex_auto_div {
            padding: 20px;
            display: flex;
            width: 100%;
            flex: 1 0 auto;
            text-transform: capitalize;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;

        }
        tr td:first-child {
            width: 169px;
        }
        tr td:last-child {
            border-bottom: 1px dotted #333;
        }
        h3, h6 {
            color: #828bb2;
        }
        h5.mb-0.mr-30.mb_xs_15px.mb_sm_20px.amount {
            border: solid 1px;
            padding: 5px;
            margin-top: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container-fluid a4_width">
    <div class="invoice_heading">
        <div class="invoice_logo">
            <img src="{{asset(app('general_setting')->logo)}}" style="max-height: 220px; max width: 500px" alt="">
        </div>
        <div class="invoice_no">
            <h5 class="hpb-1">{{app('general_setting')->company_name}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->phone}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->email}}</h5>
            <h5>{{app('general_setting')->address}}</h5>
            <h5>{{trans("common.Print")}} : {{date('m-d-Y')}}</h5>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-3">
        <div class="box_header common_table_header">
            <div class="main-title">
                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{trans('account.money_reciept')}}</h3>
                <h5 class="mb-0 mr-30 mb_xs_15px mb_sm_20px amount">{{ single_price($voucher->amount) }}</h5>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between mt-3">
        <div class="left-side">
            <h6>{{trans('account.date')}} : <span class="underline">{{ $voucher->date }}</span></h6>
        </div>
        <div class="right-side">
            <h6 class="title_width">{{trans('account.reciept_no')}} : <span class="underline">{{ $voucher->GetTypeName()." - ".$voucher->txn_id }}</span></h6>
        </div>
    </div>
    <table>
        @foreach ($voucher->transactions->where('type', 'Cr') as $key => $transaction)
            <tr>
                <td>{{trans('account.amount_recieve_from')}}</td>
                <td> : <span class="underline">{{ @$transaction->sub_leadger->name }}</span></td>
            </tr>
            <tr>
                <td>{{trans('account.address')}}</td>
                <td> : <span class="underline">
                    {{ @$transaction->sub_leadger->morphable->address }}
                </span></td>
            </tr>
        @endforeach
        <tr>
            <td>{{trans('account.amount')}}</td>
            <td> : <span class="underline"> {{ single_price($voucher->amount) }} </span>
            </td>
        </tr>
        <tr>
            <td>{{trans('account.purpose_of_payment')}}</td>
            <td> : <span class="underline">
                    {{ $voucher->narration }}
                </span>
            </td>
        </tr>
        <tr>
            <td>{{trans('account.payment_mode')}}</td>
            <td> : <span class="underline">{{ ($voucher->type == "pay_cash" || $voucher->type == "rec_cash") ? trans('account.cash') : trans('account.bank') }}</span></td>
        </tr>
    </table>
    <div class="d-flex justify-content-between mt-3">
        <h6>{{trans('account.generated_by')}} : <span class="underline">{{ $voucher->user->name }}</span></h6>
        <h6>{{trans('account.approved_by')}} : <span class="underline">{{ $voucher->approver->name }}</span></h6>
    </div>
    <div class="d-flex justify-content-between mt-3">
        <div class="left-side">
            <img src="{{ asset('frontend/img/signature.png') }}" alt="" >
            <p class="mb-0">--------------------------</p>
            <p style="margin-bottom:0; line-height:14px;">{{trans('account.authorized_signature')}}</p>
        </div>
        <div class="right-side">
            <img src="{{ asset('frontend/img/signature.png') }}" alt="" >
            <p class="mb-0">--------------------------</p>
            <p style="margin-bottom:0; line-height:14px;">{{trans('account.authorized_signature')}}</p>
        </div>
    </div>
</div>

<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
window.print();
});
</script>
</body>
</html>
