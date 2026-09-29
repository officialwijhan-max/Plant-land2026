<?php

namespace Modules\Purchase\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Employee\Entities\Payroll\Payroll;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Carbon\Carbon;

class ReturnInvoiceFromSupplierExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;

        $items = $given_data['return_invoices'];
        $supplier = $given_data['supplier'];

        $new_array = collect();

        $x = new \stdClass();
        $x->col_1 = (isset($supplier)) ? $supplier->contact_id ." - Return Invoice Lists" : "Return Invoice Lists";
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';

        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'Sl';
        $x->col_2 = 'Date';
        $x->col_3 = 'Invoice';
        $x->col_4 = 'Approve';
        $x->col_5 = 'Amount';

        $new_array->push($x);

        foreach ($items as $key => $return) {
            $x = new \stdClass();
            $x->col_1 = $key+1;
            $x->col_2 = $return->date;
            $x->col_3 = $return->invoice_no;
            if ($return->return_status == 1) {
                $x->col_4 = __('common.Approve');
            }else {
                $x->col_4 = __('common.Pending');
            }
            $x->col_5 = single_price($return->items()->sum('return_amount'));

            $new_array->push($x);
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
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A2:E2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A2:E2');
            },
        ];
    }
}
