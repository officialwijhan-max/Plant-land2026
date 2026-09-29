<?php

namespace Modules\UserActivityLog\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\UserActivityLog\Entities\LogActivity as LogActivityModel;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use DB;

class LoginActivityModelExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;

    public function collection()
    {
        $new_array = collect();
        $items = LogActivityModel::with('user')->where('login', 1)->get();
        $x = new \stdClass();
        $x->col_1 = 'Login Logout Activity';
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $x->col_7 = '';
        $new_array->push($x);
        $x = new \stdClass();
        $x->col_1 = 'SL';
        $x->col_2 = 'User';
        $x->col_3 = 'Login At';
        $x->col_4 = 'Logout At';
        $x->col_5 = 'IP';
        $x->col_6 = 'Agent';
        $x->col_7 = 'Description';
        $new_array->push($x);
        foreach ($items as $key => $item)
        {
            if (count(auth()->user()->user_col_permissions) > 0) {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'login_logout_activity')->first();
            }else {
                $permissions = null;
            }
    
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'user')) {
                    $x->col_2 = $item->user->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'login_at')) {
                    $x->col_3 = $item->login_time;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'logout_at')) {
                    $x->col_4 = ($item->logout_time) ? $item->logout_time : "";
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'ip')) {
                    $x->col_5 = $item->ip;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'agent')) {
                    $x->col_6 = $item->agent;
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'description')) {
                    $x->col_7 = $item->subject;
                }else {
                    $x->col_7 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->user->name;
                $x->col_3 = $item->login_time;
                $x->col_4 = ($item->logout_time) ? $item->logout_time : "";
                $x->col_5 = $item->ip;
                $x->col_6 = $item->agent;
                $x->col_7 = $item->subject;
                
                $new_array->push($x);
            }
        }
        return $new_array;
    }

    public function map($row): array
    {
        return [
            $row->col_1,
            $row->col_2,
            $row->col_3,
            $row->col_4,
            $row->col_5,
            $row->col_6,
            $row->col_7,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 60,
            'H' => 40,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 13]],
            2    => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A1:G1');
            },
        ];
    }
}
