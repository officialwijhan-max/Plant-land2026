<?php

namespace Modules\Product\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Carbon\Carbon;

class ProductExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'product_list')->first();
        }else {
            $permissions = null;
        }

        $items = $given_data['items'];

        $new_array = collect();

        $x = new \stdClass();
        $x->col_1 = "PRODUCT LIST";
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
        $x->col_12 = '';
        $x->col_13 = '';
        $x->col_14 = '';

        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'Sl';
        $x->col_2 = 'Image';
        $x->col_3 = 'Name';
        $x->col_4 = 'SKU';
        $x->col_5 = 'Brand';
        $x->col_6 = 'Model';
        $x->col_7 = 'Purchase Price';
        $x->col_8 = 'Selling Price';
        $x->col_9 = 'Min Price';
        $x->col_10 = 'Stock';
        $x->col_11 = 'Supplier';
        $x->col_12 = 'Product Type';
        $x->col_13 = 'Category';
        $x->col_14 = 'Stock Alert';

        $new_array->push($x);

        foreach ($items as $key => $productSkus) {
            if (@$productSkus->product->product_type == "Single" && @$productSkus->product->image_source != null) {
                $image_source = $productSkus->product->image_source ?? 'public/backEnd/img/no_image.png';
            }elseif (@$productSkus->product->product_type == "Variable" && @$productSkus->product->image_source != null) {
                $image_source = $productSkus->product_variation->image_source ?? 'public/backEnd/img/no_image.png';
            }else {
                $image_source = 'public/backEnd/img/no_image.png';
            }
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'image')) {
                    $x->col_2 = $image_source;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_3 = @$productSkus->product->product_name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'SKU')) {
                    $x->col_4 = (app('general_setting')->origin == 1) ? $productSkus->product->origin : $productSkus->sku;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'brand')) {
                    $x->col_5 = @$productSkus->product->brand->name;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'model')) {
                    $x->col_6 = @$productSkus->product->model->name;
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'purchase_price')) {
                    $x->col_7 = single_price($productSkus->purchase_price);
                }else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'selling_price')) {
                    $x->col_8 = single_price($productSkus->selling_price);
                }else {
                    $x->col_8 = '-';
                }
                if (str_contains($permissions->export_column, 'min_price')) {
                    $x->col_9 = single_price($productSkus->min_selling_price);
                }else {
                    $x->col_9 = '-';
                }
                if (str_contains($permissions->export_column, 'stock')) {
                    $x->col_10 = ($productSkus->stock()->exists()) ? $productSkus->stock->stock : 0;
                }else {
                    $x->col_10 = '-';
                }
                if (str_contains($permissions->export_column, 'supplier')) {
                    $x->col_11 = ($productSkus->item()->exists()) ? @$productSkus->item->itemable->supplier->name : 'X';
                }else {
                    $x->col_11 = '-';
                }
                if (str_contains($permissions->export_column, 'product_type')) {
                    $x->col_12 = $productSkus->product->product_type == 'Variable' ? 'Variant' : 'Single';
                }else {
                    $x->col_12 = '-';
                }
                if (str_contains($permissions->export_column, 'category')) {
                    $x->col_13 = @$productSkus->product->category->name;
                }else {
                    $x->col_13 = '-';
                }
                if (str_contains($permissions->export_column, 'stock_alert')) {
                    $x->col_14 = $productSkus->alert_quantity.' '.@$productSkus->product->unit_type->name;
                }else {
                    $x->col_14 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $image_source;
                $x->col_3 = @$productSkus->product->product_name;
                $x->col_4 = (app('general_setting')->origin == 1) ? $productSkus->product->origin : $productSkus->sku;
                $x->col_5 = @$productSkus->product->brand->name;
                $x->col_6 = @$productSkus->product->model->name;
                $x->col_7 = single_price($productSkus->purchase_price);
                $x->col_8 = single_price($productSkus->selling_price);
                $x->col_9 = single_price($productSkus->min_selling_price);
                $x->col_10 = ($productSkus->stock()->exists()) ? $productSkus->stock->stock : 0;
                $x->col_11 = ($productSkus->item()->exists()) ? @$productSkus->item->itemable->supplier->name : 'X';
                $x->col_12 = $productSkus->product->product_type == 'Variable' ? 'Variant' : 'Single';
                $x->col_13 = @$productSkus->product->category->name;
                $x->col_14 = $productSkus->alert_quantity.' '.@$productSkus->product->unit_type->name;

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
            $item->col_12,
            $item->col_13,
            $item->col_14,
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
            'B' => 35,
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
            'M' => 20,
            'N' => 20,
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
                $event->sheet->getDelegate()->getStyle('A2:N2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A2:N2');
            },
        ];
    }
}
