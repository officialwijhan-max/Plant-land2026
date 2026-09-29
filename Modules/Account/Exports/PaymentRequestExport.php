<?php

namespace Modules\Account\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentRequestExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles
{
    use Exportable;
    protected $data;
    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;
        $items = $given_data['items'];

        $new_array = collect();

        $x = new \stdClass();
        $x->col_1 = 'SL';
        $x->col_2 = 'STAFF NAME';
        $x->col_3 = 'ACCOUNTANT NAME';
        $x->col_4 = 'BANK ACCOUNT';
        $x->col_5 = 'DATE';
        $x->col_6 = 'NARRATION';
        $x->col_7 = 'REGION';
        $x->col_8 = 'AMOUNT';
        $x->col_9 = 'STATUS';
        $new_array->push($x);
        foreach ($items as $key => $item)
        {
        
            $x = new \stdClass();
            $x->col_1 = $key+1;
            $x->col_2 = $item->staff->name;
            $x->col_3 = $item->accountant->name;
            $x->col_4 = $item->bank->bank_name;
            $x->col_5 = date("d F, Y", strtotime($item->created_at));
            $x->col_6 = $item->narration;
            $x->col_7 = $item->region;
            $x->col_8 = number_format($item->amount, 2);
            $x->col_9 = $item->status;
            $new_array->push($x);
        
        }
        return $new_array;
    }

    public function map($transactions): array
    {
        return [
            $transactions->col_1,
            $transactions->col_2,
            $transactions->col_3,
            $transactions->col_4,
            $transactions->col_5,
            $transactions->col_6,
            $transactions->col_7,
            $transactions->col_8,
            $transactions->col_9,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 50,
            'D' => 25,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
