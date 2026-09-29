<!DOCTYPE html>
<html>
<head>

    <title>{{ __('attendance.Attendance Report') }} Print</title>

    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/rtl/bootstrap.min.css"/>

    <style>
        .invoice_heading {
            border-bottom: 1px solid black;
            padding: 20px;
            text-transform: capitalize;
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

        .invoice_info {
            padding: 20px;
            width: 100%;
            text-transform: capitalize;
            min-height: 100px;
        }
        table {
            text-align: left;
            font-family: "Poppins", sans-serif;
        }

        td, th {
            color: #828bb2;
            font-size: 10px;
            font-weight: 400;
            font-family: "Poppins", sans-serif;
        }

        th {
            font-weight: 600;
            font-family: "Poppins", sans-serif;
        }
        .margin_120{
            margin-top: 120px;
            font-size: 12px;
        }.margin_12{
            margin-bottom: 120px;
            font-size: 12px;
        }
        .invoice_footer{
            position: absolute;
            left: 0;
            bottom: 180px;
            width: 100%;
        }

        .invoice_info_footer {
            padding: 0px;
            width: 100%;
            left: 0;
            text-transform: capitalize;
            position: inherit;
        }

        p {
            font-size: 10px;
            color: #454545;
            line-height: 16px;
        }
        .extra_div {
            height:100;
        }
        .a4_width {
           max-width: 210mm;
           margin: auto;
        }
        h5 {
            font-size: 13px !important;
            font-weight: 500;
            line-height: 12px;
        }
        @media print{@page {size: landscape}}
    </style>
</head>
<body>
<div class="container-fluid ">
    <div class="invoice_heading">
        <div class="invoice_logo">
            <img src="{{asset(app('general_setting')->logo)}}" style="max-height: 110px; max width: 500px" alt="">
        </div>
        <div class="invoice_no">
            <h5 class="hpb-1">{{app('general_setting')->company_name}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->phone}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->email}}</h5>
            <h5>{{app('general_setting')->address}}</h5>
            <h5>{{trans("common.Print")}} : {{date('m-d-Y')}}</h5>
        </div>
    </div>
    @php
        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'attendance_report_list')->first();
        }else {
            $permissions = null;
        }
        $max_col = 0;
    @endphp
    <div class="invoice_info">
        <h5 class="text-center">{{ __('attendance.Attendance Report') }}</h5>
        <table class="table table-bordered billing_info m-0">
            <thead>
                @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                        <th scope="col">{{ __('common.ID') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'employee'))
                        <th scope="col">{{ __('common.Staff') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'staff_id'))
                        <th scope="col">{{ __('attendance.Staff ID') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'p'))
                        <th scope="col">{{ __('attendance.P') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'l'))
                        <th scope="col">{{ __('attendance.L') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'a'))
                        <th scope="col">{{ __('attendance.A') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'h'))
                        <th scope="col">{{ __('attendance.H') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'present'))
                        <th scope="col">{{ __('attendance.Present') }}</th>
                        @endif
                        @foreach ($report_dates as $key => $report_date)
                        <th scope="col">{{ $report_date->date }}</th>
                        @endforeach
                    </tr>
                @else
                    <tr>
                        <th scope="col">{{ __('common.ID') }}</th>
                        <th scope="col">{{ __('common.Staff') }}</th>
                        <th scope="col">{{ __('attendance.Staff ID') }}</th>
                        <th scope="col">{{ __('attendance.P') }}</th>
                        <th scope="col">{{ __('attendance.L') }}</th>
                        <th scope="col">{{ __('attendance.A') }}</th>
                        <th scope="col">{{ __('attendance.H') }}</th>
                        <th scope="col">{{ __('attendance.Present') }}</th>
                        @foreach ($report_dates as $key => $report_date)
                        <th scope="col">{{ $report_date->date }}</th>
                        @endforeach
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach ($items as $key => $user)
                    @php
                        $total_attendance = 0;
                        $total_days_of_month = count($report_dates);
                        $absent = count($user->attendances->where('month', $m)->where('year', $y)->where('attendance', 'A'));
                        $late = count($user->attendances->where('month', $m)->where('year', $y)->where('attendance', 'L'));
                        $half_day = count($user->attendances->where('month', $m)->where('year', $y)->where('attendance', 'F'));
                        $present = count($user->attendances->where('month', $m)->where('year', $y)->where('attendance', 'P'));
                        $Totalpresent = ($late + $half_day + $present);
                        if ($total_days_of_month > 0) {
                            $total_attendance = ($Totalpresent * 100) / $total_days_of_month;
                        }
                    @endphp
                    @if ($permissions)
                        <tr>
                            @if (str_contains($permissions->export_column, 'id'))
                                <td>{{ $key+1 }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'employee'))
                                <td>{{ $user->name }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'staff_id'))
                                <td>{{ @$user->staff->employee_id }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'p'))
                                <td>{{ $present }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'l'))
                                <td>{{ $late }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'a'))
                                <td>{{ $absent }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'h'))
                                <td>{{ $half_day }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'present'))
                                <td>
                                    @if($user->attendances)
                                        {{ number_format($total_attendance, 2) }} %
                                    @else
                                        00
                                    @endif
                                </td>
                            @endif
                            @php
                                $attendances = $user->attendances->where('month', $m)->where('year', $y);
                                $max_col_1 = count($attendances);
                                if ($max_col < $max_col_1) {
                                    $max_col = $max_col_1;
                                }else {
                                    $max_diff = $max_col - $max_col_1;
                                }
                            @endphp
                            @if (sizeof($attendances) > 0 && sizeof($attendances) == $max_col)
                                @foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance)
                                    <td>{{ $attendance->attendance }}</td>
                                @endforeach
                            @elseif (sizeof($attendances) > 0 && sizeof($attendances) < $max_col)
                                @foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance)
                                    <td>{{ $attendance->attendance }}</td>
                                @endforeach
                                @for ($i=$max_col_1; $i < $max_col; $i++)
                                    <td></td>
                                @endfor
                            @else
                                @for ($i=0; $i < $max_diff; $i++)
                                    <td></td>
                                @endfor
                            @endif
                        </tr>
                    @else
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ @$user->staff->employee_id }}</td>
                            <td>{{ $present }}</td>
                            <td>{{ $late }}</td>
                            <td>{{ $absent }}</td>
                            <td>{{ $half_day }}</td>
                            <td>
                                @if($user->attendances)
                                    {{ number_format($total_attendance, 2) }} %
                                @else
                                    00
                                @endif
                            </td>
                            @php
                                $attendances = $user->attendances->where('month', $m)->where('year', $y);
                                $max_col_1 = count($attendances);
                                if ($max_col < $max_col_1) {
                                    $max_col = $max_col_1;
                                }else {
                                    $max_diff = $max_col - $max_col_1;
                                }
                            @endphp
                            @if (sizeof($attendances) > 0 && sizeof($attendances) == $max_col)
                                @foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance)
                                    <td>{{ $attendance->attendance }}</td>
                                @endforeach
                            @elseif (sizeof($attendances) > 0 && sizeof($attendances) < $max_col)
                                @foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance)
                                    <td>{{ $attendance->attendance }}</td>
                                @endforeach
                                @for ($i=$max_col_1; $i < $max_col; $i++)
                                    <td></td>
                                @endfor
                            @else
                                @for ($i=0; $i < $max_diff; $i++)
                                    <td></td>
                                @endfor
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        window.print();
    });
</script>
</body>
</html>
