
@php
    $max_col = 0;
@endphp
<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        @if(strpos($_SERVER['REQUEST_URI'], '?') == true)
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=print" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=csv" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @else
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=print" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=csv" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @endif
        <a href="#" title="Col Show/Hide" class="hide_show_click_btn">
            <i class="ti-layout-column3"></i>
        </a>
    </x-slot>

    <x-slot name="table">

        <x-table.head>
            <tr>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.ID') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="name" href="#">
                        @if (request('sort') == 'asc' && request('col') == "name")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "name")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Staff') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('attendance.Staff ID') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('attendance.P') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('attendance.L') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('attendance.A') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('attendance.H') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('attendance.Present') }}
                    </a>
                </x-table.th>
                @foreach ($report_dates as $key => $report_date)
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="id" href="#">
                    <i class="ti-arrow-down"></i>{{ $report_date->date }}</a>
                </x-table.th>
                @endforeach
            </tr>
        </x-table.head>

        <x-table.body>
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
                <x-table.tr>
                    <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                    <x-table.td><a href="#">{{ $user->name }}</a> </x-table.td>
                    <x-table.td>{{ @$user->staff->employee_id }}</x-table.td>
                    <x-table.td>{{ $present }}</x-table.td>
                    <x-table.td>{{ $late }}</x-table.td>
                    <x-table.td>{{ $absent }}</x-table.td>
                    <x-table.td>{{ $half_day }}</x-table.td>
                    <x-table.td>
                        @if($user->attendances)
                            {{ number_format($total_attendance, 2) }} %
                        @else
                            00
                        @endif
                    </x-table.td>
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
                            <x-table.td>{{ $attendance->attendance }}</x-table.td>
                        @endforeach
                    @elseif (sizeof($attendances) > 0 && sizeof($attendances) < $max_col)
                        @foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance)
                           <x-table.td>{{ $attendance->attendance }}</x-table.td>
                        @endforeach
                        @for ($i=$max_col_1; $i < $max_col; $i++)
                            <x-table.td></x-table.td>
                        @endfor
                    @else
                        @for ($i=0; $i < $max_diff; $i++)
                            <x-table.td></x-table.td>
                        @endfor
                    @endif
                </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
