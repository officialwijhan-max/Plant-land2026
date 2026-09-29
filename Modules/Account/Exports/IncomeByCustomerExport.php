<?php

namespace Modules\Account\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class IncomeByCustomerExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;
        $dateFrom = $given_data['dateFrom'];
        $dateTo = $given_data['dateTo'];
        $items = $given_data['items'];

        $new_array = collect();
        
        $x = new \stdClass();
        $x->col_1 = "Income By Customer";
        $x->col_2 = "";
        $x->col_3 = "";
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Name';
        $x->col_3 = 'Income';
        $new_array->push($x);


        foreach ($items as $key => $account) {
            if($dateFrom==null && $dateTo==null) {
                $transactions = $account->Credit;
            }else{
                $transactions = $account->transactions()->whereBetween('created_at',[$dateFrom, $dateTo])->where('type', 'Dr')->sum('amount');
            }
            $x = new \stdClass();
            $x->col_1 = $key+1;
            $x->col_2 = $account->name;
            $x->col_3 = number_format($transactions, 2);
            $new_array->push($x);
        }
        return $new_array;
    }

    public function map($row): array
    {
        return [
            $row->col_1,
            $row->col_2,
            $row->col_3,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 35,
            'C' => 20,
            'D' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 11]],
            2    => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:C1')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A1:C1');
            },
        ];
    }
}
