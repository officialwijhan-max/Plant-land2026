<?php

namespace Modules\ProAccount\Export;
use update\Modules\ProAccount\Repositories\BalanceSheetRepository;
use update\Modules\ProAccount\Repositories\FinancialYearRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class BalanceSheetReportExports implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
{
    use Exportable;

    protected $data;

    function __construct($data) {
        $this->given_data = $data;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 13]],
            "A"    => ['font' => ['bold' => true]],
        ];
    }

    public function collection()
    {
        $given_data = $this->given_data;

        $showroom_id = $given_data['showroom_id'];

        $assets = $given_data['assets'];
        $assets_ids = $given_data['assets_ids'];

        $liabilities = $given_data['liabilities'];
        $liabilities_ids = $given_data['liabilities_ids'];

        if (($key = array_search(Settings('retail_earning_leadger'), $liabilities_ids)) !== false) {
            unset($liabilities_ids[$key]);
        }
        // $inventory_manual = $given_data['inventory_manual'];

        $all_financial_years = $given_data['all_financial_years'];
        $financial_years = $given_data['financial_years'];

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "Account Name";
        $x->col_2 = "Note";
        foreach ($financial_years as $financial_year) {
            $to_date = ($financial_year->end_date != null) ? date(Settings("date_format_id"), strtotime($financial_year->end_date)) : date(Settings("date_format_id"), strtotime(\Carbon\Carbon::now()->format('Y-m-d')));
            $x->col_3[] = $to_date;
        }
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "Assets type Account";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        foreach ($assets as $key => $asset) {
            if ($asset->parent_id != 0) {
                $x = new \stdClass();
                $x->col_1 = $asset->name;
                $x->col_2 = "";
                foreach ($financial_years as $financial_year) {
                    $x->col_3[] = "";
                }
                $new_array->push($x);
            }

            foreach ($asset->childrenCategories as $child) {
                if (count($child->transactionsForFinancialYear()) > 0) {
                    $x = new \stdClass();
                    $x->col_1 = $child->name;
                    $x->col_2 = ($child->is_cost_center == 0) ? $child->code : "";
                    foreach ($financial_years as $financial_year) {
                        $x->col_3[] = ($child->is_cost_center == 0) ? single_price($child->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
                    }
                    $new_array->push($x);
                }
                if ($child->categories) {
                    $new_array = $this->getChildren($child->categories, $financial_years, $new_array, $showroom_id);
                }
            }

            $x = new \stdClass();
            $x->col_1 = "";
            $x->col_2 = "";
            foreach ($financial_years as $financial_year) {
                $x->col_3[] = "";
            }
            $new_array->push($x);
        }

        $x = new \stdClass();
        $x->col_1 = "Total Assets";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($financial_year->showTotalBalance($assets_ids));
        }
        $new_array->push($x);


        for ($i=0; $i < 2 ; $i++) {
            $x = new \stdClass();
            $x->col_1 = "";
            $x->col_2 = "";
            foreach ($financial_years as $financial_year) {
                $x->col_3[] = "";
            }
            $new_array->push($x);
        }

        $x = new \stdClass();
        $x->col_1 = "Liabilities & Equities";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);


        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        foreach ($liabilities as $key => $liability) {
            if ($asset->parent_id != 0) {
                $x = new \stdClass();
                $x->col_1 = $liability->name;
                $x->col_2 = "";
                foreach ($financial_years as $financial_year) {
                    $x->col_3[] = "";
                }
                $new_array->push($x);
            }

            foreach ($liability->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $child) {
                if (count($child->transactionsForFinancialYear()) > 0) {
                    $x = new \stdClass();
                    $x->col_1 = $child->name;
                    $x->col_2 = ($child->is_cost_center == 0) ? $child->code : "";
                    foreach ($financial_years as $financial_year) {
                        $x->col_3[] = ($child->is_cost_center == 0) ? single_price($child->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
                    }
                    $new_array->push($x);
                }
                if ($child->categories) {
                    $new_array = $this->getChildren($child->categories->whereNotIn('id',[Settings('retail_earning_leadger')]), $financial_years, $new_array, $showroom_id);
                }
            }
            $x = new \stdClass();
            $x->col_1 = "";
            $x->col_2 = "";
            foreach ($financial_years as $financial_year) {
                $x->col_3[] = "";
            }
            $new_array->push($x);
        }

        $x = new \stdClass();
        $x->col_1 = "Total Liabilities and Equity";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($financial_year->showTotalBalance($liabilities_ids, $showroom_id));
        }
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "Approximate Profit Loss";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($financial_year->showTotalBalance($assets_ids, $showroom_id) - $financial_year->showTotalBalance($liabilities_ids, $showroom_id));
        }
        $new_array->push($x);


        return $new_array;
    }

    public function map($row): array
    {
        $data = [];
        array_push($data, $row->col_1);
        array_push($data, $row->col_2);
        foreach ($row->col_3 as $key => $col_value) {
            array_push($data, $col_value);
        }
        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50,
            'B' => 30,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25,
            'I' => 25,
            'J' => 25,
            'K' => 25,
            'L' => 25,
            'M' => 25,
            'N' => 25,
            'O' => 25,
            'P' => 25,
            'Q' => 25,
            'R' => 25,
            'S' => 25,
            'T' => 25,
            'U' => 25,
            'V' => 25,
            'W' => 25,
            'X' => 25,
            'Y' => 25,
            'Z' => 25,
        ];
    }

    public function getChildren($childrens, $financial_years, $new_array, $showroom_id)
    {
        foreach ($childrens as $child_account) {
            if (count($child_account->transactionsForFinancialYear()) > 0) {
                $x = new \stdClass();
                $x->col_1 = $child_account->name;
                $x->col_2 = ($child_account->is_cost_center == 0) ? $child_account->code : "";
                foreach ($financial_years as $financial_year) {
                    $x->col_3[] = ($child_account->is_cost_center == 0) ? single_price($child_account->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
                }
                $new_array->push($x);
            }
            if ($child_account->categories) {
                $new_array = $this->getChildren2($child_account->categories->whereNotIn('id',[Settings('retail_earning_leadger')]), $financial_years, $new_array, $showroom_id);
            }
        }
        return $new_array;
    }

    public function getChildren2($childrens, $financial_years, $new_array, $showroom_id)
    {
        foreach ($childrens as $child_account) {
            if (count($child_account->transactionsForFinancialYear()) > 0) {
                $x = new \stdClass();
                $x->col_1 = $child_account->name;
                $x->col_2 = ($child_account->is_cost_center == 0) ? $child_account->code : "";
                foreach ($financial_years as $financial_year) {
                    $x->col_3[] = ($child_account->is_cost_center == 0) ? single_price($child_account->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
                }
                $new_array->push($x);
            }
            if ($child_account->categories) {
                $new_array = $this->getChildren3($child_account->categories->whereNotIn('id',[Settings('retail_earning_leadger')]), $financial_years, $new_array, $showroom_id);
            }
        }
        return $new_array;
    }

    public function getChildren3($childrens, $financial_years, $new_array, $showroom_id)
    {
        foreach ($childrens as $child_account) {
            if (count($child_account->transactionsForFinancialYear()) > 0) {
                $x = new \stdClass();
                $x->col_1 = $child_account->name;
                $x->col_2 = ($child_account->is_cost_center == 0) ? $child_account->code : "";
                foreach ($financial_years as $financial_year) {
                    $x->col_3[] = ($child_account->is_cost_center == 0) ? single_price($child_account->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
                }
                $new_array->push($x);
            }
            if ($child_account->categories) {
                $new_array = $this->getChildren4($child_account->categories->whereNotIn('id',[Settings('retail_earning_leadger')]), $financial_years, $new_array, $showroom_id);
            }
        }
        return $new_array;
    }

    public function getChildren4($childrens, $financial_years, $new_array, $showroom_id)
    {
        foreach ($childrens as $child_account) {
            if (count($child_account->transactionsForFinancialYear()) > 0) {
                $x = new \stdClass();
                $x->col_1 = $child_account->name;
                $x->col_2 = ($child_account->is_cost_center == 0) ? $child_account->code : "";
                foreach ($financial_years as $financial_year) {
                    $x->col_3[] = ($child_account->is_cost_center == 0) ? single_price($child_account->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
                }
                $new_array->push($x);
            }
        }
        return $new_array;
    }
}
