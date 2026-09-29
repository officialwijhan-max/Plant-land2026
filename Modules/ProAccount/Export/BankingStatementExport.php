<?php

namespace Modules\ProAccount\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Account\Entities\CashFlowDetail;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use DB;

class BankingStatementExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings, WithEvents
{
    use Exportable;

    protected $data;

    function __construct($data) {
        $this->given_data = $data;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function drawings()
   {
       $drawing = new Drawing();
       $drawing->setName(Settings("company_name"));
       $drawing->setPath(public_path('/frontend/img/logo.png'));
       $drawing->setHeight(60);
       $drawing->setCoordinates('A1');
       return $drawing;
   }

    public function collection()
    {
        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'banking_list')->first();
        }else {
            $permissions = null;
        }

        $items = $this->given_data['items'];
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "Banking";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "";
        $x->col_5 = "";
        $x->col_6 = "";
        $new_array->push($x);
        $x = new \stdClass();
        $x->col_1 = "Sl";
        $x->col_2 = "Date";
        $x->col_3 = "Account";
        $x->col_4 = "Balance";
        $x->col_5 = "Reconciled";
        $x->col_6 = "Uploaded By";
        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_2 = $item->date;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'account')) {
                    $x->col_3 = $item->leadger->account;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'balance')) {
                    $x->col_4 = single_price($item->balance);
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'reconciled')) {
                    $x->col_5 = ($item->re_conciled == 1) ? trans("common.Closed") : trans("common.Open");
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'uploaded_by')) {
                    $x->col_6 = $item->user->name;
                }else {
                    $x->col_6 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->date;
                $x->col_3 = $item->leadger->account;
                $x->col_4 = single_price($item->balance);
                $x->col_5 = ($item->re_conciled == 1) ? trans("common.Closed"); : trans("common.Open");;
                $x->col_6 = $item->user->name;
                
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
            $row->col_6
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 20,
            'C' => 30,
            'D' => 25,
            'E' => 20,
            'F' => 30,
            'G' => 30,
            'H' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            6    => ['font' => ['bold' => true, 'size' => 12]],
            7    => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A6:F6')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A6:F6');
            },
        ];
    }
}
