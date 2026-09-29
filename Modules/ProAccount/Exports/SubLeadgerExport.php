<?php

namespace Modules\ProAccount\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\ProAccount\Entities\SubLeadger;
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

class SubLeadgerExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings
{
    use Exportable;

    protected $ledger_id, $type;

    function __construct($ledger_id, $type) {
        $this->ledger = $ledger_id;
        $this->type = $type;
    }

    public function collection()
    {
        $ledger_data = $this->ledger;
        $type_data = $this->type;
        $items = SubLeadger::query();

        if ($ledger_data)
        {
            $items = $items->where('leadger_id', $ledger_data);
        }

        $items = $items->latest()->get();

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = null;
            if ($type_data == "account_recievable_list") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'account_recievable_list')->first();
            }
            if ($type_data == "account_payable_list") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'account_recievable_list')->first();
            }
        }else {
            $permissions = null;
        }

        $new_array = collect();
        $datas = array();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Code';
        $x->col_3 = 'Name';
        $x->col_4 = 'Status';

        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'code')) {
                    $x->col_2 = $item->code;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_3 = $item->name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_4 = ($item->is_active == 1) ? trans('common.Active') : trans('common.Inactive');
                }else {
                    $x->col_4 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->code;
                $x->col_3 = $item->name;
                $x->col_4 = ($item->is_active == 1) ? trans('common.Active') : trans('common.Inactive');

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
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            8    => ['font' => ['bold' => true]]
        ];
    }
}
