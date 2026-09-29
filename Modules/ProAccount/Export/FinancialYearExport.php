<?php

namespace Modules\ProAccount\Export;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Account\Entities\FinancialYear;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Modules\Employee\Repositories\Attendance\AttendanceRepository;
use Carbon\Carbon;

class FinancialYearExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings
{
    use Exportable;

    function __construct() {
        //
    }

    public function collection()
    {
        $items = FinancialYear::query();
        $items = $items->latest()->get();

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'financial_years_list')->first();
        }else {
            $permissions = null;
        }

        $new_array = collect();
        $datas = array();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Start Date';
        $x->col_3 = 'End Date';
        $x->col_4 = 'Is Locked';

        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'start_date')) {
                    $x->col_2 = $item->start_date;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'end_date')) {
                    $x->col_3 = ($item->end_date) ? showDate($item->end_date) : "";
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'is_locked')) {
                    $x->col_4 = ($item->is_locked == 0) ? __('account.Open') : __('account.Closed');
                }else {
                    $x->col_4 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->start_date;
                $x->col_3 = ($item->end_date) ? $item->end_date : "";
                $x->col_4 = ($item->is_locked == 0) ? __('account.Open') : __('account.Closed');

                $new_array->push($x);
            }
        }
        return $new_array;
    }

    public function map($item): array
    {
        return [
            $item->col_1,
            $item->col_2,
            $item->col_3,
            $item->col_4,
        ];
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function drawings()
   {
       $drawing = new Drawing();
       $drawing->setName(Settings("company_name"));
       $drawing->setPath(public_path('/frontend/img/logo.png'));
       $drawing->setHeight(70);
       $drawing->setCoordinates('A3');
       return $drawing;
   }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 25,
            'D' => 20,
            'E' => 20,
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            8    => ['font' => ['bold' => true]]
        ];
    }
}
