<?php

namespace Modules\ProAccount\Exports;
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

class PartnerAccountExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings
{
    use Exportable;

    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $items = $this->data;

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'sub_leadger_list')->first();
        }else {
            $permissions = null;
        }

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Type';
        $x->col_3 = 'Ledger';
        $x->col_4 = 'Code';
        $x->col_5 = 'Name';
        $x->col_6 = 'Balance';
        $x->col_7 = 'Status';

        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'type')) {
                    $x->col_2 = $item->leadger->TypeName;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'ledger')) {
                    $x->col_3 = $item->leadger->name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'code')) {
                    $x->col_4 = $item->code;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_5 = $item->name;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'balance')) {
                    $x->col_6 = single_price($item->BalanceAmount);
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_7 = ($item->is_active == 1) ? trans('common.Active') : trans('common.Inactive');
                }else {
                    $x->col_7 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->leadger->TypeName;
                $x->col_3 = $item->leadger->name;
                $x->col_4 = $item->code;
                $x->col_5 = $item->name;
                $x->col_6 = single_price($item->BalanceAmount);
                $x->col_7 = ($item->is_active == 1) ? trans('common.Active') : trans('common.Inactive');

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
            $item->col_5,
            $item->col_6,
            $item->col_7,
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
            'G' => 20,
            'H' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            8    => ['font' => ['bold' => true]]
        ];
    }
}
