<?php

namespace Modules\Purchase\Exports;
use Maatwebsite\Excel\Concerns\FromQuery;
use Modules\Purchase\Entities\CNF;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CNFExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;

    public function query()
    {
        return CNF::query();
    }

    public function map($item): array
    {
        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'cnf_list')->first();
        }else {
            $permissions = null;
        }

        if ($permissions) {
            $data = [];
            if (str_contains($permissions->export_column, 'id')) {
                array_push($data, $item->id);
            }
            if (str_contains($permissions->export_column, 'name')) {
                array_push($data, $item->name);
            }
            if (str_contains($permissions->export_column, 'address')) {
                array_push($data, $item->address);
            }
            if (str_contains($permissions->export_column, 'email')) {
                array_push($data, $item->email);
            }
            if (str_contains($permissions->export_column, 'phone')) {
                array_push($data, $item->phone);
            }
            if (str_contains($permissions->export_column, 'status')) {
                array_push($data, ($item->status == 1) ? "Approved" : "Pending");
            }
            return $data;
        }else {
            return [
                $item->id,
                $item->name,
                $item->address,
                $item->email,
                $item->phone,
                $item->status
             ];
        }
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function headings(): array
    {
        return [
            'SL',
            'Name',
            'Address',
            'Email',
            'Phone',
            'Status'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
