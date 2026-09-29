<?php

namespace Modules\ProAccount\Exports;
use Maatwebsite\Excel\Concerns\FromQuery;
use Modules\Account\Entities\CashFLowAccount;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use DB;

class CashFLowAccountExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings
{
    use Exportable;

    public function query()
    {
        return CashFLowAccount::query();
    }

    public function map($item): array
    {
        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'cash_flow_account_list')->first();
        }else {
            $permissions = null;
        }

        if ($permissions) {
            $data = [];
            if (str_contains($permissions->export_column, 'id')) {
                array_push($data, $item->id);
            }
            if (str_contains($permissions->export_column, 'type')) {
                array_push($data, ($item->type == 3) ? trans('account.expense') : trans('account.income'));
            }
            if (str_contains($permissions->export_column, 'code')) {
                array_push($data, $item->code);
            }
            if (str_contains($permissions->export_column, 'name')) {
                array_push($data, $item->name);
            }
            if (str_contains($permissions->export_column, 'status')) {
                array_push($data, ($item->is_active == 1) ? trans('common.Active') : trans('common.Inactive'));
            }
            return $data;
        }else {
            return [
                $item->id,
                ($item->type == 3) ? trans('account.expense') : trans('account.income'),
                $item->code,
                $item->name,
                ($item->is_active == 1) ? trans('common.Active') : trans('common.Inactive')
             ];
        }
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'SL',
            'Type',
            'Code',
            'Name',
            'Status'
        ];
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
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 13,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            8    => ['font' => ['bold' => true]]
        ];
    }
}
