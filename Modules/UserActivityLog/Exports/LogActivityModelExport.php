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

class LogActivityModelExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;

    public function collection()
    {
        $new_array = collect();
        $items = LogActivityModel::with('user')->where('login', 0)->get();
        $x = new \stdClass();
        $x->col_1 = 'Activity Log';
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $x->col_7 = '';
        $x->col_8 = '';
        $new_array->push($x);
        $x = new \stdClass();
        $x->col_1 = 'SL';
        $x->col_2 = 'Description';
        $x->col_3 = 'Type';
        $x->col_4 = 'URL';
        $x->col_5 = 'IP';
        $x->col_6 = 'Agent';
        $x->col_7 = 'Attempted At';
        $x->col_8 = 'User';
        $new_array->push($x);
        foreach ($items as $key => $item)
        {
            if (count(auth()->user()->user_col_permissions) > 0) {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'activity_logs')->first();
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
                if (str_contains($permissions->export_column, 'description')) {
                    $x->col_2 = $item->subject;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'type')) {
                    if ($item->type == 0) {
                        $x->col_3 = 'Error';
                    } elseif ($item->type == 1) {
                        $x->col_3 = 'Success';
                    } elseif ($item->type == 2) {
                        $x->col_3 = 'Warning';
                    } else {
                        $x->col_3 = 'Info';
                    }
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'url')) {
                    $x->col_4 = $item->url;
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
                if (str_contains($permissions->export_column, 'attempted_at')) {
                    $x->col_7 = $item->updated_at;
                }else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'user')) {
                    $x->col_8 = $item->user->name;
                }else {
                    $x->col_8 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->subject;
                if ($item->type == 0) {
                    $x->col_3 = 'Error';
                } elseif ($item->type == 1) {
                    $x->col_3 = 'Success';
                } elseif ($item->type == 2) {
                    $x->col_3 = 'Warning';
                } else {
                    $x->col_3 = 'Info';
                }
                $x->col_4 = $item->url;
                $x->col_5 = $item->ip;
                $x->col_6 = $item->agent;
                $x->col_7 = $item->updated_at;
                $x->col_8 = $item->user->name;
                
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
            $row->col_8,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 50,
            'C' => 20,
            'D' => 40,
            'E' => 30,
            'F' => 30,
            'G' => 30,
            'H' => 30,
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
                $event->sheet->getDelegate()->getStyle('A1:H1')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A1:H1');
            },
        ];
    }
}
