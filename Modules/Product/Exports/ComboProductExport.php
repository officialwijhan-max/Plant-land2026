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

class ComboProductExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithEvents
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'combo_product_list')->first();
        }else {
            $permissions = null;
        }

        $items = $given_data['combo_items'];

        $new_array = collect();

        $x = new \stdClass();
        $x->col_1 = "COMBO PRODUCT LIST";
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $x->col_7 = '';

        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'Sl';
        $x->col_2 = 'Image';
        $x->col_3 = 'Name';
        $x->col_4 = 'Price';
        $x->col_5 = 'Regular Price';
        $x->col_6 = 'Total Product';
        $x->col_7 = 'Status';

        $new_array->push($x);

        foreach ($items as $key => $combo_item) {
            if (@$combo_item->image_source != null) {
                $image_source = $combo_item->image_source;
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
                    $x->col_3 = $combo_item->name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'price')) {
                    $x->col_4 = single_price($combo_item->price);
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'regular_price')) {
                    $x->col_5 = single_price($combo_item->total_regular_price);
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'total_product')) {
                    $x->col_6 = count($combo_item->combo_products).' '. trans('common.pcs');
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_7 = ($combo_item->status == 0) ? trans('common.Closed') : trans('common.Open');
                }else {
                    $x->col_7 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $image_source;
                $x->col_3 = $combo_item->name;
                $x->col_4 = single_price($combo_item->price);
                $x->col_5 = single_price($combo_item->total_regular_price);
                $x->col_6 = count($combo_item->combo_products).' '. trans('common.pcs');
                $x->col_7 = ($combo_item->status == 0) ? trans('common.Closed') : trans('common.Open');

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
                $event->sheet->getDelegate()->getStyle('A2:G2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A2:G2');
            },
        ];
    }
}
