<?php

namespace Modules\Product\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Core\Entities\Product\ProductHistory;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class OpeningStockAddExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;
    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'opening_stock_add_list')->first();
        }else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'OPENING STOCK ADD LIST';
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $x->col_7 = '';
        $x->col_8 = '';
        $x->col_9 = '';
        $x->col_10 = '';
        $x->col_11 = '';
        $new_array->push($x);
        $x = new \stdClass();
        $x->col_1 = 'SL';
        $x->col_2 = 'Date';
        $x->col_3 = 'Name';
        $x->col_4 = 'SKU/Part Number';
        $x->col_5 = 'Brand';
        $x->col_6 = 'Model';
        $x->col_7 = 'Branch';
        $x->col_8 = 'Purchase Price';
        $x->col_9 = 'Sell Price';
        $x->col_10 = 'Stock';
        $x->col_11 = 'Created User';
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
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_3 = @$item->productSku->product->product_name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'SKU')) {
                    $x->col_4 = (app('general_setting')->origin == 1) ? @$item->productSku->product->origin : @$item->productSku->sku;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'brand')) {
                    $x->col_5 = @$item->productSku->product->brand->name;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'model')) {
                    $x->col_6 = @$item->productSku->product->model->name;
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'showroom')) {
                    $x->col_7 = @$item->itemable->name;
                }else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'purchase_price')) {
                    $x->col_8 = single_price(@$item->productSku->purchase_price) . '/' . @$item->productSku->product->unit_type->name;
                }else {
                    $x->col_8 = '-';
                }
                if (str_contains($permissions->export_column, 'selling_price')) {
                    $x->col_9 = single_price(@$item->productSku->selling_price) . '/' . @$item->productSku->product->unit_type->name;
                }else {
                    $x->col_9 = '-';
                }
                if (str_contains($permissions->export_column, 'stock')) {
                    $x->col_10 = $item->in_out;
                }else {
                    $x->col_10 = '-';
                }
                if (str_contains($permissions->export_column, 'created_user')) {
                    $x->col_11 = userName($item->created_by);
                }else {
                    $x->col_11 = '-';
                }
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->date;
                $x->col_3 = $item->productSku->product->product_name;
                $x->col_4 = (app('general_setting')->origin == 1) ? @$item->productSku->product->origin : @$item->productSku->sku;
                $x->col_5 = @$item->productSku->product->brand->name;
                $x->col_6 = @$item->productSku->product->model->name;
                $x->col_7 = @$item->itemable->name;
                $x->col_8 = single_price(@$item->productSku->purchase_price) . '/' . @$item->productSku->product->unit_type->name;
                $x->col_9 = single_price(@$item->productSku->selling_price) . '/' . @$item->productSku->product->unit_type->name;
                $x->col_10 = $item->in_out;
                $x->col_11 = userName($item->created_by);
                $new_array->push($x);
            }
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
            $item->col_11,
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
            'B' => 20,
            'C' => 30,
            'D' => 30,
            'E' => 20,
            'F' => 20,
            'G' => 30,
            'H' => 30,
            'I' => 30,
            'J' => 30,
            'K' => 15,
            'L' => 30,
            'M' => 10,
            'N' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A2:N2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A2:N2');
            },
        ];
    }
}
