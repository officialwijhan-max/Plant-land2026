<?php

namespace Modules\Attendance\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Modules\Employee\Repositories\Attendance\AttendanceRepository;
use Carbon\Carbon;

class AttendanceReportExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;

    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $requested_data = $this->data;
        $items = $requested_data['items'];
        $max_col = 0;
        $r = $requested_data['r'];
        $m = $requested_data['m'];
        $y = $requested_data['y'];


        $report_dates = $requested_data['report_dates'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'attendance_report_list')->first();
        }else {
            $permissions = null;
        }

        $total_num_of_days = Carbon::now()->month(date('m',strtotime($m)))->daysInMonth;

        $new_array = collect();
        $datas = array();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Staff';
        $x->col_3 = 'Staff ID';
        $x->col_4 = 'P';
        $x->col_5 = 'L';
        $x->col_6 = 'A';
        $x->col_7 = 'H';
        $x->col_8 = 'Present';
        foreach ($report_dates as $report_date) {
            array_push($datas, $report_date->date);
        }
        $x->col_9 = $datas;

        $new_array->push($x);

        foreach ($items as $key => $user) {
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

            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'employee')) {
                    $x->col_2 = $user->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'staff_id')) {
                    $x->col_3 = @$user->staff->employee_id;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'p')) {
                    $x->col_4 = $present;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'l')) {
                    $x->col_5 = $late;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'a')) {
                    $x->col_6 = $absent;
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'h')) {
                    $x->col_7 = $half_day;
                }else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'present')) {
                    $x->col_8 = ($user->attendances) ? number_format($total_attendance, 2).' %' : "00 %";
                }else {
                    $x->col_8 = '-';
                }
                $attendances = $user->attendances->where('month', $m)->where('year', $y);
                $max_col_1 = count($attendances);
                if ($max_col < $max_col_1) {
                    $max_col = $max_col_1;
                }else {
                    $max_diff = $max_col - $max_col_1;
                }
                if (sizeof($attendances) > 0 && sizeof($attendances) == $max_col) {
                    foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance) {
                        $x->col_9[] = $attendance->attendance;
                    }
                }elseif (sizeof($attendances) > 0 && sizeof($attendances) < $max_col) {
                    foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance) {
                        $x->col_9[] = $attendance->attendance;
                    }
                    for ($i=$max_col_1; $i < $max_col; $i++) {
                        $x->col_9[] = 'X';
                    }
                }else {
                    for ($i=0; $i < $max_diff; $i++) {
                        $x->col_9[] = 'X';
                    }
                }
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $user->name;
                $x->col_3 = @$user->staff->employee_id;
                $x->col_4 = $present;
                $x->col_5 = $late;
                $x->col_6 = $absent;
                $x->col_7 = $half_day;
                $x->col_8 = ($user->attendances) ? number_format($total_attendance, 2).' %' : "00 %";
                $attendances = $user->attendances->where('month', $m)->where('year', $y);
                $max_col_1 = count($attendances);
                if ($max_col < $max_col_1) {
                    $max_col = $max_col_1;
                }else {
                    $max_diff = $max_col - $max_col_1;
                }
                if (sizeof($attendances) > 0 && sizeof($attendances) == $max_col) {
                    foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance) {
                        $x->col_9[] = $attendance->attendance;
                    }
                }elseif (sizeof($attendances) > 0 && sizeof($attendances) < $max_col) {
                    foreach ($user->attendances->where('month', $m)->where('year', $y) as $attendance) {
                        $x->col_9[] = $attendance->attendance;
                    }
                    for ($i=$max_col_1; $i < $max_col; $i++) {
                        $x->col_9[] = 'X';
                    }
                }else {
                    for ($i=0; $i < $max_diff; $i++) {
                        $x->col_9[] = 'X';
                    }
                }
                $new_array->push($x);
            }
        }
        return $new_array;
    }

    public function map($item): array
    {
        $data = [];
        array_push($data, $item->col_1);
        array_push($data, $item->col_2);
        array_push($data, $item->col_3);
        array_push($data, $item->col_4);
        array_push($data, $item->col_5);
        array_push($data, $item->col_6);
        array_push($data, $item->col_7);
        array_push($data, $item->col_8);
        if (!empty($item->col_9)) {
            foreach ($item->col_9 as $key => $col_value) {
                array_push($data, $col_value);
            }
        }
        return $data;
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 20,
            'D' => 5,
            'E' => 5,
            'F' => 5,
            'G' => 5,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,
            'N' => 10,
            'O' => 10,
            'P' => 10,
            'Q' => 10,
            'R' => 10,
            'S' => 10,
            'T' => 10,
            'U' => 10,
            'V' => 10,
            'W' => 10,
            'X' => 10,
            'Y' => 10,
            'Z' => 10,
            'AA' => 10,
            'AB' => 10,
            'AC' => 10,
            'AD' => 10,
            'AE' => 10,
            'AF' => 10,
            'AG' => 10,
            'AH' => 10,
            'AI' => 10,
            'AJ' => 10,
            'AK' => 10,
            'AL' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            8    => ['font' => ['bold' => true]]
        ];
    }
}
