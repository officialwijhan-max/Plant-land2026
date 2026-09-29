<?php

namespace Modules\Sale\Exports;
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

class ReturnInvoiceFromCustomerExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    protected $data, $user_type;

    function __construct($data, $user_type) {
        $this->data = $data;
        $this->user_type = $user_type;
    }

    public function collection()
    {
        $given_data = $this->data;
        $given_user_type = $this->user_type;

        $items = $given_data['return_invoices'];
        $customer = ($given_user_type == "agent") ? $given_data['agent'] : $given_data['customer'];

        $new_array = collect();

        $x = new \stdClass();
        if ($given_user_type == "agent") {
            $x->col_1 = (isset($customer)) ? $customer->user->name ." - Return Invoice Lists" : "Return Invoice Lists";
        } else {
            $x->col_1 = (isset($customer)) ? $customer->contact_id ." - Return Invoice Lists" : "Return Invoice Lists";
        }

        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $x->col_7 = '';

        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'Sl';
        $x->col_2 = 'Date';
        $x->col_3 = 'Invoice';
        $x->col_4 = 'Reference No';
        $x->col_5 = 'Sold By';
        $x->col_6 = 'Approval';
        $x->col_7 = 'Amount';

        $new_array->push($x);

        foreach ($items as $key => $return) {
            $x = new \stdClass();
            $x->col_1 = $key+1;
            $x->col_2 = $return->date;
            $x->col_3 = $return->invoice_no;
            $x->col_4 = $return->ref_no;
            $x->col_5 = $return->user->name;
            if ($return->return_status == 1) {
                $x->col_6 = __('purchase.Yes');
            }else {
                $x->col_6 = __('purchase.No');
            }
            $x->col_7 = number_format($return->items()->sum('return_amount'), 2);

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
                $event->sheet->getDelegate()->getStyle('A2:G2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A2:G2');
            },
        ];
    }
}
