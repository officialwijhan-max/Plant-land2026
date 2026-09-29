@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{ __('extrauser::extrauser.'.$contact->contact_type) }} {{__('common.Profile')}}</h3>
                            </div>

                            <ul class="d-flex">
                                <li><a class="primary-btn radius_30px mr-10 fix-gr-bg"
                                       href="{{route('extrauser.edit',$contact->id)}}"><i
                                            class="ti-pen"></i>{{ __('common.Edit') }}</a></li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-lg-5 col-sm-12">
                                <img class="student-meta-img img-100 mb-3"
                                     src="{{ file_exists(@$contact->avatar) ? asset(@$contact->avatar) : asset('public/img/profile.jpg') }}"
                                     alt="">
                                <h3>{{$contact->name}}</h3>
                                <table class="table table-borderless supplier_view">
                                    <tr>
                                        <td>{{ __('common.Name') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $contact->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Email') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $contact->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Phone') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $contact->mobile }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Pay Term') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $contact->pay_term }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Pay Condition') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $contact->pay_term_condition }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Address') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $contact->address }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('contact.Country') }}</td>
                                        <td>: <span class="ml-1"></span>{{ @$contact->country->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('contact.State') }}:</td>
                                        <td>: <span class="ml-1"></span>{{ @$contact->state->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('contact.City') }}:</td>
                                        <td>: <span class="ml-1"></span>{{ @$contact->city->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Tax Number') }}</td>
                                        <td>: <span class="ml-1"></span>{{ $contact->tax_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Opening Balance') }}</td>
                                        <td>: <span class="ml-1"></span>{{ single_price($contact->opening_balance) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Registered Date') }}</td>
                                        <td>: <span
                                                class="ml-1"></span>{{ showDate($contact->created_at) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('common.Active Status') }}</td>
                                        <td>: <span class="ml-1"></span>
                                            @if ($contact->is_active == 1)
                                                <span class="badge_1">{{__('common.Active')}}</span>
                                            @else
                                                <span class="badge_4">{{__('common.DeActive')}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-1 col-lg-1 col-sm-12"></div>

                        </div>
                        <hr>
                        @if ($contact->note != null)
                            <div class="row">
                                <div class="col">
                                    <label class="primary_input_label" for="">
                                        @php
                                            echo $contact->note
                                        @endphp
                                    </label>
                                </div>
                            </div>
                            <hr>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="Voucher_info">

    </div>
    <div id="getDetails">

    </div>
@endsection
@push("scripts")
    <script type="text/javascript">

    </script>
@endpush
