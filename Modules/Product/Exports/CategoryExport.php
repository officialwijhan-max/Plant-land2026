<?php

namespace Modules\Product\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class CategoryExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;
        $items = $given_data['items'];

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'Category List';
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'Category ID';
        $x->col_2 = 'Name';
        $x->col_3 = 'Code';
        $x->col_4 = 'Parent';
        $x->col_5 = 'Description';
        $x->col_6 = 'Status';
        $new_array->push($x);

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'category_list')->first();
        }else {
            $permissions = null;
        }
        foreach ($items as $key => $category_value)
        {

            if ($permissions) {
                $x = new \stdClass();

                if (str_contains($permissions->export_column, 'id')) {
                    $hypen = "";
                    if ($category_value->level > 0) {
                        for ($i = 1; $i < $category_value->level; $i++) {
                            $hypen .= "-";
                        }
                    }
                    $x->col_1 = $hypen.''.$category_value->id;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $hypen = "";
                    if ($category_value->level > 0) {
                        for ($i = 1; $i < $category_value->level; $i++) {
                            $hypen .= "-";
                        }
                    }
                    $x->col_2 = $hypen.''.$category_value->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'code')) {
                    $x->col_3 = $category_value->code;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'parent')) {
                    $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'description')) {
                    $x->col_5 = $category_value->description;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                }else {
                    $x->col_6 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $hypen = "";
                if ($category_value->level > 0) {
                    for ($i = 1; $i < $category_value->level; $i++) {
                        $hypen .= "-";
                    }
                }

                $x->col_1 = $hypen.''.$category_value->id;
                $x->col_2 = $hypen.''.$category_value->name;
                $x->col_3 = $category_value->code;
                $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                $x->col_5 = $category_value->description;
                $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                $new_array->push($x);
            }
            if(count($category_value->categories) > 0) {
                foreach ($category_value->categories as $key => $category_value)
                {
                    if ($permissions) {
                        $x = new \stdClass();
                        $hypen = "";
                        if (str_contains($permissions->export_column, 'id')) {
                            $hypen = "";
                            if ($category_value->level > 0) {
                                for ($i = 1; $i < $category_value->level; $i++) {
                                    $hypen .= "-";
                                }
                            }
                            $x->col_1 = $hypen.''.$category_value->id;
                        }else {
                            $x->col_1 = '-';
                        }
                        if (str_contains($permissions->export_column, 'name')) {
                            if ($category_value->level > 0) {
                                for ($i = 1; $i < $category_value->level; $i++) {
                                    $hypen .= "-";
                                }
                            }
                            $x->col_2 = $hypen.''.$category_value->name;
                        }else {
                            $x->col_2 = '-';
                        }
                        if (str_contains($permissions->export_column, 'code')) {
                            $x->col_3 = $category_value->code;
                        }else {
                            $x->col_3 = '-';
                        }
                        if (str_contains($permissions->export_column, 'parent')) {
                            $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                        }else {
                            $x->col_4 = '-';
                        }
                        if (str_contains($permissions->export_column, 'description')) {
                            $x->col_5 = $category_value->description;
                        }else {
                            $x->col_5 = '-';
                        }
                        if (str_contains($permissions->export_column, 'status')) {
                            $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                        }else {
                            $x->col_6 = '-';
                        }

                        $new_array->push($x);
                    }else {
                        $x = new \stdClass();
                        if ($category_value->level > 0) {
                            for ($i = 1; $i < $category_value->level; $i++) {
                                $hypen .= "-";
                            }
                        }
                        $x->col_1 = $hypen.''.$category_value->id;
                        $x->col_2 = $hypen.''.$category_value->name;
                        $x->col_3 = $category_value->code;
                        $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                        $x->col_5 = $category_value->description;
                        $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                        $new_array->push($x);
                    }
                    if(count($category_value->categories) > 0) {
                        foreach ($category_value->categories as $key => $category_value)
                        {
                            if ($permissions) {
                                $x = new \stdClass();
                                $hypen = "";
                                if (str_contains($permissions->export_column, 'id')) {
                                    $hypen = "";
                                    if ($category_value->level > 0) {
                                        for ($i = 1; $i < $category_value->level; $i++) {
                                            $hypen .= "-";
                                        }
                                    }
                                    $x->col_1 = $hypen.''.$category_value->id;
                                }else {
                                    $x->col_1 = '-';
                                }
                                
                                if (str_contains($permissions->export_column, 'name')) {
                                    if ($category_value->level > 0) {
                                        for ($i = 1; $i < $category_value->level; $i++) {
                                            $hypen .= "-";
                                        }
                                    }
                                    $x->col_2 = $hypen.''.$category_value->name;
                                }else {
                                    $x->col_2 = '-';
                                }
                                if (str_contains($permissions->export_column, 'code')) {
                                    $x->col_3 = $category_value->code;
                                }else {
                                    $x->col_3 = '-';
                                }
                                if (str_contains($permissions->export_column, 'parent')) {
                                    $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                                }else {
                                    $x->col_4 = '-';
                                }
                                if (str_contains($permissions->export_column, 'description')) {
                                    $x->col_5 = $category_value->description;
                                }else {
                                    $x->col_5 = '-';
                                }
                                if (str_contains($permissions->export_column, 'status')) {
                                    $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                                }else {
                                    $x->col_6 = '-';
                                }

                                $new_array->push($x);
                            }else {
                                $x = new \stdClass();
                                if ($category_value->level > 0) {
                                    for ($i = 1; $i < $category_value->level; $i++) {
                                        $hypen .= "-";
                                    }
                                }
                                $x->col_1 = $hypen.''.$category_value->id;
                                $x->col_2 = $hypen.''.$category_value->name;
                                $x->col_3 = $category_value->code;
                                $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                                $x->col_5 = $category_value->description;
                                $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                                $new_array->push($x);
                            }

                            if(count($category_value->categories) > 0) {
                                foreach ($category_value->categories as $key => $category_value)
                                {
                                    if ($permissions) {
                                        $x = new \stdClass();
                                        $hypen = "";
                                        if (str_contains($permissions->export_column, 'id')) {
                                            $hypen = "";
                                            if ($category_value->level > 0) {
                                                for ($i = 1; $i < $category_value->level; $i++) {
                                                    $hypen .= "-";
                                                }
                                            }
                                            $x->col_1 = $hypen.''.$category_value->id;
                                        }else {
                                            $x->col_1 = '-';
                                        }
                                       
                                        if (str_contains($permissions->export_column, 'name')) {
                                            if ($category_value->level > 0) {
                                                for ($i = 1; $i < $category_value->level; $i++) {
                                                    $hypen .= "-";
                                                }
                                            }
                                            $x->col_2 = $hypen.''.$category_value->name;
                                        }else {
                                            $x->col_2 = '-';
                                        }
                                        if (str_contains($permissions->export_column, 'code')) {
                                            $x->col_3 = $category_value->code;
                                        }else {
                                            $x->col_3 = '-';
                                        }
                                        if (str_contains($permissions->export_column, 'parent')) {
                                            $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                                        }else {
                                            $x->col_4 = '-';
                                        }
                                        if (str_contains($permissions->export_column, 'description')) {
                                            $x->col_5 = $category_value->description;
                                        }else {
                                            $x->col_5 = '-';
                                        }
                                        if (str_contains($permissions->export_column, 'status')) {
                                            $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                                        }else {
                                            $x->col_6 = '-';
                                        }

                                        $new_array->push($x);
                                    }else {
                                        $x = new \stdClass();
                                        if ($category_value->level > 0) {
                                            for ($i = 1; $i < $category_value->level; $i++) {
                                                $hypen .= "-";
                                            }
                                        }
                                        $x->col_1 = $hypen.''.$category_value->id;
                                        $x->col_2 = $hypen.''.$category_value->name;
                                        $x->col_3 = $category_value->code;
                                        $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                                        $x->col_5 = $category_value->description;
                                        $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                                        $new_array->push($x);
                                    }


                                    if(count($category_value->categories) > 0) {
                                        foreach ($category_value->categories as $key => $category_value)
                                        {
                                            if ($permissions) {
                                                $x = new \stdClass();
                                                $hypen = "";
                                                if (str_contains($permissions->export_column, 'id')) {
                                                    $hypen = "";
                                                    if ($category_value->level > 0) {
                                                        for ($i = 1; $i < $category_value->level; $i++) {
                                                            $hypen .= "-";
                                                        }
                                                    }
                                                    $x->col_1 = $hypen.''.$category_value->id;
                                                }else {
                                                    $x->col_1 = '-';
                                                }
                                               
                                                if (str_contains($permissions->export_column, 'name')) {
                                                    if ($category_value->level > 0) {
                                                        for ($i = 1; $i < $category_value->level; $i++) {
                                                            $hypen .= "-";
                                                        }
                                                    }
                                                    $x->col_2 = $hypen.''.$category_value->name;
                                                }else {
                                                    $x->col_2 = '-';
                                                }
                                                if (str_contains($permissions->export_column, 'code')) {
                                                    $x->col_3 = $category_value->code;
                                                }else {
                                                    $x->col_3 = '-';
                                                }
                                                if (str_contains($permissions->export_column, 'parent')) {
                                                    $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                                                }else {
                                                    $x->col_4 = '-';
                                                }
                                                if (str_contains($permissions->export_column, 'description')) {
                                                    $x->col_5 = $category_value->description;
                                                }else {
                                                    $x->col_5 = '-';
                                                }
                                                if (str_contains($permissions->export_column, 'status')) {
                                                    $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                                                }else {
                                                    $x->col_6 = '-';
                                                }

                                                $new_array->push($x);
                                            }else {
                                                $x = new \stdClass();
                                                if ($category_value->level > 0) {
                                                    for ($i = 1; $i < $category_value->level; $i++) {
                                                        $hypen .= "-";
                                                    }
                                                }
                                                $x->col_1 = $hypen.''.$category_value->id;
                                                $x->col_2 = $hypen.''.$category_value->name;
                                                $x->col_3 = $category_value->code;
                                                $x->col_4 = @$category_value->parentCat->name ?? 'N/A';
                                                $x->col_5 = $category_value->description;
                                                $x->col_6 = ($category_value->status == 1) ? __('common.Active') : __('common.DeActive');
                                                $new_array->push($x);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $new_array;
    }

    public function trDraw($subcategories, $permissions)
    {
        $new_array = collect();

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
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 20,
            'C' => 30,
            'D' => 60,
            'E' => 20,
            'F' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            2    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:E1')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A1:E1');
            },
        ];
    }
}
