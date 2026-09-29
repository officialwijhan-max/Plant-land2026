@extends('extrauser::layouts.master')
@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__("extrauser::extrauser.".$contact_type)}} </h3>
                    <ul class="d-flex">
                        @if(permissionCheck('add_contact.store'))
                            <li><a class="primary-btn radius_30px mr-10 fix-gr-bg"
                                   href="{{route("extrauser.create", ['contact_type' => 'Agency'])}}"><i
                                        class="ti-plus"></i>{{__('extrauser::extrauser.New '.$contact_type)}}</a>
                            </li>
                        @endif
                        @if(permissionCheck('contact_csv_upload.store'))
                            <li><a class="primary-btn radius_30px mr-10 fix-gr-bg"
                                   href="{{route('contact_csv_upload.create')}}"><i
                                        class="ti-export"></i>{{__('common.Upload Via CSV')}}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <!-- table-responsive -->
                    <div class="">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th scope="col">{{__('common.Sl')}}</th>
                                <th scope="col">{{__('common.Contact ID')}}</th>
                                <th scope="col">{{__('common.Name')}}</th>
                                <th scope="col">{{__('common.Email')}}</th>
                                <th scope="col">{{__('common.Phone')}}</th>
                                <th scope="col">{{__('common.Pay Term')}}</th>
                                <th scope="col">{{__('common.Tax Number')}}</th>
                                <th scope="col">{{ __('setting.Active') }}</th>
                                <th scope="col">{{__('common.Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($contacts as $key => $contact)
                                <tr>
                                    <th>{{$key+1}}</th>
                                    <td>{{$contact->contact_id}}</td>
                                    <td>{{$contact->name}}</td>
                                    <td>{{$contact->email}}</td>
                                    <td>{{$contact->mobile}}</td>
                                    <td>{{$contact->pay_term}} {{$contact->pay_term_condition}} </td>
                                    <td>{{$contact->tax_number}}</td>
                                    <td>
                                        <label class="switch_toggle" for="active_checkbox{{ $contact->id }}">
                                            <input type="checkbox" id="active_checkbox{{ $contact->id }}" @if ($contact->is_active == 1) checked @endif value="{{ $contact->id }}" onchange="update_active_status(this)" {{ permissionCheck('languages.update_active_status') ? '' : 'disabled' }}>
                                            <div class="slider round"></div>
                                        </label>
                                    </td>
                                    <td>
                                        <!-- shortby  -->
                                        <div class="dropdown CRM_dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                {{__('common.Select')}}
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                                                <a href="{{route('extrauser.edit',$contact->id)}}" class="dropdown-item" type="button">{{__('common.Edit')}}</a>
                                                <a href="{{route('extrauser.show',$contact->id)}}" class="dropdown-item" type="button">{{__('common.View')}}</a>
                                                <a onclick="confirm_modal('{{route('add_contact.delete',$contact->id)}}');" class="dropdown-item ">{{__('common.Delete')}}</a>

                                            </div>
                                        </div>
                                        <!-- shortby  -->
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @include('backEnd.partials.delete_modal')

    <!--/ Import Lead -->
    </div>
@endsection

@push("scripts")
    <script type="text/javascript">
        function update_active_status(el){
            "use strict";
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('contact.update_active_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    toastr.success(trans('js.Updated Successfully'), trans('js.Success'));
                }
                else{
                    toastr.error(trans('js.Something is not right'));
                }
            });
        }
    </script>
@endpush
