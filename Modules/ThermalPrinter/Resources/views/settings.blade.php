@extends('thermalprinter::layouts.master')

@section('mainContent')


    @if(session()->has('message-success'))
        <div class="alert alert-success mb-25" role="alert">
            {{ session()->get('message-success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session()->has('message-danger'))
        <div class="alert alert-danger">
            {{ session()->get('message-danger') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{ __('thermal_printer.Thermal Printer Settings')}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <!-- Prefix  -->
                        <form action="{{route("thermalprinter.saveSettings")}}" method="POST"
                              enctype="multipart/form-data" id="content_form">
                            @csrf
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                <i class="fa fa-print mr-1"></i> {{ __('thermal_printer.printerConnectionSettingsTitle') }}
                            </legend>
                            <div class="row form">
                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                               for="">{{ __('thermal_printer.connectorTypeLabel') }} *</label>
                                        <select name="connector_type" class="primary_select mb-15">
                                            <option value="windows"
                                                    @if(!empty($data->connector_type)) @if($data->connector_type == "windows") selected="selected" @endif @endif>
                                                Windows
                                            </option>
                                            <option value="cups"
                                                    @if(!empty($data->connector_type)) @if($data->connector_type == "cups") selected="selected" @endif @endif>
                                                Linux or MacOS
                                            </option>
                                            <option value="network"
                                                    @if(!empty($data->connector_type)) @if($data->connector_type == "network") selected="selected" @endif @endif>
                                                Network
                                            </option>
                                        </select>
                                        <span class="text-danger">{{$errors->first('connector_type')}}</span>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label
                                            class="primary_input_label"> {{ __('thermal_printer.connectorDescriptorLabel') }}
                                            *</label>

                                        <input type="text" class="primary_input_field" name="connector_descriptor"
                                               required
                                               value="{{ !empty($data->connector_descriptor) ? $data->connector_descriptor  : ''}}">
                                        @if(Lang::get('thermal_printer.connectorDescriptorHelpMessage') != "NULL")
                                            <span
                                                class="text-muted"> {{ (__('thermal_printer.connectorDescriptorHelpMessage')) }} </span>
                                        @else
                                            <span
                                                class="text-muted">Enter printer name if your Connector Type is <b>Windows/Linux/MacOS</b></span>
                                            <br>
                                            <span class="text-muted">Enter the IP address or Samba URI, <b>e.g: smb://192.168.1.12/PrinterName</b> if your Connector Type is <b>Network</b></span>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                               for="">{{ __('thermal_printer.printerWidthLabel') }} *</label>
                                        <select name="print_width" class="primary_select mb-15">
                                            <option value="3" @if(!empty($data->print_width)) @if($data->print_width == "3") selected="selected" @endif @endif>3 Inch (80mm)</option>
                                            <option value="2" @if(!empty($data->print_width)) @if($data->print_width == "2") selected="selected" @endif @endif>2 Inch (58mm)</option>
                                        </select>
                                        <span class="text-danger">{{$errors->first('print_width')}}</span>
                                    </div>
                                </div>
                            </div>


                            <div class="row form">

                                <div class="col-12 text-center">
                                    <button class="primary-btn semi_large2 fix-gr-bg" type="submit"><i
                                            class="ti-check"></i>{{__('common.Save')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
