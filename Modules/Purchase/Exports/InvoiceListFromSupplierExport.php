<?php

namespace Modules\Purchase\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Carbon\Carbon;

class InvoiceListFromSupplierExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;

        $items = $given_data['invoices'];
        $customer = $given_data['supplier'];

        $new_array = collect();

        $x = new \stdClass();
        $x->col_1 = (isset($supplier)) ? $supplier->contact_id ." - Invoice Lists" : "Invoice Lists";
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $x->col_7 = '';
        $x->col_8 = '';
        $x->col_9 = '';
        $x->col_10 = '';

        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'Sl';
        $x->col_2 = 'Date';
        $x->col_3 = 'Invoice';
        $x->col_4 = 'Reference No';
        $x->col_5 = 'Purchased By';
        $x->col_6 = 'Approval';
        $x->col_7 = 'Paid Status';
        $x->col_8 = 'Total Amount';
        $x->col_9 = 'Paid';
        $x->col_10 = 'Due';

        $new_array->push($x);

        foreach ($items as $key => $sale) {
            $x = new \stdClass();
            $x->col_1 = $key+1;
            $x->col_2 = $sale->date;
            $x->col_3 = $sale->invoice_no;
            $x->col_4 = $sale->ref_no;
            $x->col_5 = $sale->user->name;
            if ($sale->status == 1) {
                $x->col_6 = __('purchase.Yes');
            }else {
                $x->col_6 = __('purchase.No');
            }
            if ($sale->is_paid == 0) {
                $x->col_7 = __('sale.Unpaid');
            }elseif ($sale->is_paid == 1) {
                $x->col_7 = __('sale.Partial');
            }else {
                $x->col_7 = __('sale.Paid');
            }
            $x->col_8 = single_price($sale->payable_amount);
            $x->col_9 = single_price($sale->payments()->where('payment_type','pay')->sum('amount')-$sale->payments()->where('payment_type','return')->sum('amount'));
            $x->col_10 = single_price($sale->payable_amount - $sale->payments()->where('payment_type','pay')->sum('amount') - $sale->payments()->where('payment_type','return')->sum('amount'));

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
            $item->col_6,
            $item->col_7,
            $item->col_8,
            $item->col_9,
            $item->col_10,
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
                $event->sheet->getDelegate()->getStyle('A2:J2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A2:J2');
            },
        ];
    }
}
