@extends('backEnd.master')

@section('mainContent')

<style>
    .main-wrapper .fstElement {
        width: 100%;
        height: 45px;
        border-radius: 20px;
        font-size: 10px;
    }
</style>

<section class="admin-visitor-area up_st_admin_visitor">

    <div class="container-fluid p-0">

        <div class="row justify-content-center">

            <div class="col-12">

                <div class="box_header">

                    <div class="main-title d-flex">

                        <h3 class="mb-0 mr-30">{{ __('common.Edit Staff Info') }}</h3>

                    </div>

                </div>

            </div>

            <div class="col-12">

                <div class="white_box_50px box_shadow_white">

                    <form action="{{ route('staffs.update', $staff->user->id) }}" id="staff_addForm" method="POST" enctype="multipart/form-data">

                        @csrf

                        @method('PATCH')

                        <input type="hidden" name="user_id" value="{{$staff->user_id}}">

                        <div class="row">

                            <div class="col-xl-12">

                                <div class="main-title d-flex">

                                    <h3 class="mb-0 mr-30">{{ __('common.Basic Info') }}</h3>

                                </div>

                            </div>

                            <hr>



                            <div class="col-xl-6 employee_id_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Staff Id') }} *</label>

                                    <input name="employee_id" id="employee_id" class="primary_input_field name" placeholder="{{ __('common.Staff Id') }}" value="{{ $staff->employee_id }}" type="text" required>

                                    <span class="text-danger">{{$errors->first('employee_id')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Name') }} *</label>

                                    <input name="name" class="primary_input_field name" placeholder="{{ __('common.Name') }}" value="{{ @$staff->user->name }}" type="text" required>

                                    <span class="text-danger">{{$errors->first('name')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Email') }}</label>

                                    <input name="email" class="primary_input_field name" placeholder="{{ __('common.Email') }}" value="{{ @$staff->user->email }}" type="email">

                                    <span class="text-danger">{{$errors->first('email')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('retailer.Phone') }} </label>

                                    <input name="username" class="primary_input_field name" placeholder="{{ __('retailer.Phone') }}" value="{{ @$staff->user->username }}" type="text" >

                                    <span class="text-danger">{{$errors->first('username')}}</span>

                                </div>

                            </div>

                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('common.Password') }} ({{ trans('common.Minimum 8 Letter') }}) *</label>
                                    <div style="position: relative;">
                                        <input name="password" id="password" class="primary_input_field name" placeholder="{{ __('common.Password') }}" type="password" min="8">
                                        <span id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <span class="text-danger" id="passwordError"></span>
                                </div>
                            </div>
                            
                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('common.Confirm Password') }} ({{ trans('common.Minimum 8 Letter') }}) *</label>
                                    <div style="position: relative;">
                                        <input name="password_confirmation" id="password_confirmation" class="primary_input_field name" placeholder="{{ __('common.Confirm Password') }}" type="password" min="8">
                                        <span id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <span class="text-danger" id="confirmPasswordError"></span>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('inventory.Warehouse') }}</label>
                                    <select class="multipleSelect mb-25" name="warehouses[]" id="warehouse_id" multiple>
                                        @php
                                            // Decode the JSON field to get the selected warehouse IDs as an array
                                            $selectedWarehouses = json_decode($staff->warehouses, true) ?? [];
                                        @endphp
                                        @foreach (\Modules\Inventory\Entities\WareHouse::all() as $warehouse)
                                            <option value="{{ $warehouse->id }}" 
                                                @if (in_array($warehouse->id, $selectedWarehouses)) selected @endif>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('warehouses') }}</span>
                                </div>
                            </div>
                            



                            <div class="col-xl-6">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('department.Department') }} *</label>

                                    <select class="primary_select mb-25" name="department_id" id="department_id" required>

                                        @foreach (\Modules\Setup\Entities\Department::all() as $key => $department)

                                            <option value="{{ $department->id }}" @if ($department->id == $staff->department_id) selected @endif>{{ $department->name }}</option>

                                        @endforeach

                                    </select>

                                    <span class="text-danger">{{$errors->first('department_id')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('showroom.Branch') }} *</label>
                                    <select class="multipleSelect mb-25" name="branches[]" id="showroom_id" required multiple>
                                        @php
                                            // Decode the JSON field to get the selected branch IDs as an array
                                            $selectedBranches = json_decode($staff->branches, true) ?? [];
                                        @endphp
                                        @foreach (\Modules\Inventory\Entities\ShowRoom::where('status', 1)->get() as $showroom)
                                            <option value="{{ $showroom->id }}" 
                                                @if (in_array($showroom->id, $selectedBranches)) selected @endif>
                                                {{ $showroom->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('branches') }}</span>
                                </div>
                            </div>
                            



                            @if($staff->user->role_id != 1)

                                <div class="col-xl-6">

                                    <div class="primary_input mb-25">

                                        <label class="primary_input_label" for="">{{ __('role.Role') }} *</label>

                                        <select class="primary_select mb-25" name="role_id" id="role_id" required >

                                            @foreach ($roles as $key => $role)

                                                <option value="{{ $role->id }}-{{ $role->type }}" @if ($role->id == $staff->user->role_id) selected @endif>{{ $role->name }}</option>

                                            @endforeach

                                        </select>

                                        <span class="text-danger">{{$errors->first('role_id')}}</span>

                                    </div>

                                </div>

                                @endif

                            <div class="col-xl-6 date_of_birth_div">

                                <div class="primary_input mb-15">

                                    <label class="primary_input_label" for="">{{ __('common.Date of Birth') }} *</label>

                                    <div class="primary_datepicker_input">

                                        <div class="no-gutters input-right-icon">

                                            <div class="col">

                                                <div class="">

                                                    <input placeholder="Date" class="primary_input_field primary-input date form-control" id="date_of_birth" type="text" name="date_of_birth" value="{{ date('m/d/Y', strtotime($staff->date_of_birth)) }}" autocomplete="off" required>

                                                </div>

                                            </div>

                                            <button class="" type="button">

                                                <i class="ti-calendar" id="start-date-icon"></i>

                                            </button>

                                        </div>

                                    </div>

                                    <span class="text-danger">{{$errors->first('date_of_birth')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6 current_address_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Current Address') }}</label>

                                    <input name="current_address" id="current_address" class="primary_input_field name" placeholder="{{ __('common.Current Address') }}" value="{{ $staff->current_address }}" type="text">

                                    <span class="text-danger">{{$errors->first('current_address')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6 permanent_address_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Permanent Address') }}</label>

                                    <input name="permanent_address" id="permanent_address" class="primary_input_field name" placeholder="{{ __('common.Permanent Address') }}" value="{{ $staff->permanent_address }}" type="text">

                                    <span class="text-danger">{{$errors->first('permanent_address')}}</span>

                                </div>

                            </div>



                            <div class="col-lg-6 opening_balance_div">

                                <div class="primary_input mb-15">

                                    <label class="primary_input_label" for="">{{__('common.Opening Balance')}}</label>

                                    <input type="number" min="0" step="0.01" name="opening_balance" class="primary_input_field"

                                           value="{{ $staff->opening_balance }}" readonly>

                                    <span class="text-danger">{{$errors->first('opening_balance')}}</span>

                                </div>

                            </div>



                            <div class="col-lg-6">

                                <div class="primary_input mb-15">

                                    <label class="primary_input_label" for="">{{ __('common.Avatar') }}</label>

                                    <div class="primary_file_uploader">

                                        <input class="primary-input" type="text" id="placeholderFileOneName" placeholder="{{ __('common.Browse file') }}" readonly="">

                                        <button class="" type="button">

                                            <label class="primary-btn small fix-gr-bg" for="document_file_1">{{ __('common.Browse') }}</label>

                                            <input type="file" class="d-none" name="photo" id="document_file_1">

                                        </button>

                                    </div>

                                    <span class="text-danger">{{$errors->first('photo')}}</span>





                                </div>

                            </div>



                            <div class="col-lg-6">

                                <div class="primary_input mb-15">

                                    <label class="primary_input_label" for="">{{ __('common.Signature') }}</label>

                                    <div class="primary_file_uploader">

                                        <input class="primary-input" type="text" id="placeholderFileOneName" placeholder="{{ __('common.Browse file') }}" readonly="">

                                        <button class="" type="button">

                                            <label class="primary-btn small fix-gr-bg" for="document_file_2">{{ __('common.Browse') }}</label>

                                            <input type="file" class="d-none" name="signature_photo" id="document_file_2">

                                        </button>

                                    </div>

                                    <span class="text-danger">{{$errors->first('photo')}}</span>





                                </div>

                            </div>



                            <div class="col-xl-12 mt-5 bank_info_div">

                                <div class="main-title d-flex">

                                    <h3 class="mb-0 mr-30">{{ __('common.Bank Info') }}</h3>

                                </div>

                            </div>



                            <hr>



                            <div class="col-xl-6 bank_name_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Bank Name') }}</label>

                                    <input name="bank_name" id="bank_name" class="primary_input_field name" placeholder="{{ __('common.Bank Name') }}" value="{{ $staff->bank_name }}" type="text">

                                    <span class="text-danger">{{$errors->first('bank_name')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6 bank_branch_name_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Bank Branch Name') }}</label>

                                    <input name="bank_branch_name" id="bank_branch_name" class="primary_input_field name" placeholder="{{ __('common.Bank Branch Name') }}" value="{{ $staff->bank_branch_name }}" type="text">

                                    <span class="text-danger">{{$errors->first('bank_branch_name')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6 bank_account_name_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Account Name') }}</label>

                                    <input name="bank_account_name" id="bank_account_name" class="primary_input_field name" placeholder="{{ __('common.Account Name') }}" value="{{ $staff->bank_account_name }}" type="text">

                                    <span class="text-danger">{{$errors->first('bank_account_name')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6 bank_account_no_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Bank Account Number') }}</label>

                                    <input name="bank_account_no" id="bank_account_no" class="primary_input_field name" placeholder="{{ __('common.Bank Account Number') }}" value="{{ $staff->bank_account_no }}" type="text">

                                    <span class="text-danger">{{$errors->first('bank_account_no')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-12 mt-5 payroll_info_div">

                                <div class="main-title d-flex">

                                    <h3 class="mb-0 mr-30">{{ __('common.Payroll Info') }}</h3>

                                </div>

                            </div>

                            <hr>



                            <div class="col-xl-6 date_of_joining_div">

                                <div class="primary_input mb-15">

                                    <label class="primary_input_label" for="">{{ __('common.Date of Joining') }} *</label>

                                    <div class="primary_datepicker_input">

                                        <div class="no-gutters input-right-icon">

                                            <div class="col">

                                                <div class="">

                                                    <input placeholder="Date" class="primary_input_field primary-input date form-control" id="date_of_joining" type="text" name="date_of_joining" value="{{ date('m/d/Y', strtotime($staff->date_of_joining)) }}" autocomplete="off" required>

                                                </div>

                                            </div>

                                            <button class="" type="button">

                                                <i class="ti-calendar" id="start-date-icon"></i>

                                            </button>

                                        </div>

                                    </div>

                                    <span class="text-danger">{{$errors->first('date_of_joining')}}</span>

                                </div>

                            </div>

                            <div class="col-xl-6 basic_salary_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Basic Salary') }}</label>

                                    <input name="basic_salary" id="basic_salary" class="primary_input_field name" placeholder="{{ __('common.Basic Salary') }}" type="number" value="{{ $staff->basic_salary }}"

                                           {{ $staff->id != 1 ? 'required' : '' }}>

                                    <span class="text-danger">{{$errors->first('basic_salary')}}</span>

                                </div>

                            </div>



                            <div class="col-xl-6 employee_type_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Employment Type') }}</label>

                                    <select class="primary_select mb-25" name="employment_type" id="employment_type" onchange="getField()">

                                        <option value="">{{ __('common.Select One') }}</option>

                                        <option value="Provision" @if ($staff->employment_type == "Provision") selected @endif>{{ __('common.Provision') }}</option>

                                        <option value="Contract" @if ($staff->employment_type == "Contract") selected @endif>{{ __('common.Contract') }}</option>

                                        <option value="Permanent" @if ($staff->employment_type == "Permanent") selected @endif>{{ __('common.Permanent') }}</option>

                                    </select>

                                    <span class="text-danger">{{$errors->first('employment_type')}}</span>

                                </div>

                            </div>

                            <div class="col-xl-6 provisional_time_div">

                                <div class="primary_input mb-25">

                                    <label class="primary_input_label" for="">{{ __('common.Provision Time') }} <small>({{ __('common.In Months') }})</small> </label>

                                    <input name="provisional_months" id="provisional_time" class="primary_input_field name" placeholder="0" type="number" value="{{ $staff->provisional_months }}" required>

                                    <span class="text-danger">{{$errors->first('provisional_months')}}</span>

                                </div>

                            </div>





                            <div class="col-lg-12 text-center">

                                <div class="d-flex justify-content-center pt_20">

                                    <button type="button" onclick="fromSubmit()" class="primary-btn semi_large2 fix-gr-bg" id="save_button_parent"><i class="ti-check"></i>{{ __('common.Update') }}

                                    </button>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>



@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('password_confirmation');
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');

        // Toggle password visibility
        togglePassword.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Toggle confirm password visibility
        toggleConfirmPassword.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    function fromSubmit() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');

        // Clear previous error messages
        passwordError.textContent = '';
        confirmPasswordError.textContent = '';

        // Validate password minimum length (8 characters) and confirm password match
        // if (password.length < 8) {
        //     passwordError.textContent = '{{ trans('Password must be at least 8 characters long') }}';
        //     return false;
        // }

        if (password !== confirmPassword) {
            confirmPasswordError.textContent = '{{ trans('Passwords do not match') }}';
            return false;
        }

        // Submit the form if everything is valid
        document.getElementById('staff_addForm').submit();
        return true;
    }
</script>
<script type="text/javascript">

    $(document).ready(function(){

        getField();

        $(document).on('keyup', '#password', function(){

            if ($(this).val()){

                $('#password_confirmation').attr('required', 'true');

                $("label[for='password_confirmation']").addClass('required');

            } else{

                $('#password_confirmation').attr('required', 'false');

                $("label[for='password_confirmation']").removeClass('required');

            }

        })

    });

    function getField()

    {

        var employment_type = $('#employment_type').val();

        if (employment_type == "Provision") {

            $("#provisional_time").removeAttr("disabled");

        }

        else if (employment_type == "Contract") {

            $("#provisional_time").attr('disabled', true);

        }

        else {

            $("#bank_name").attr('Permanent', true);

            $("#provisional_time").attr('disabled', true);

        }

    }



</script>

@endpush

