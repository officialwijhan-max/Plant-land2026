<?php

namespace Modules\ProAccount\Export;
use update\Modules\ProAccount\Repositories\IncomeStatementRepository;
use update\Modules\ProAccount\Repositories\FinancialYearRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class IncomeStatementReportExports implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
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
            "B"    => ['font' => ['bold' => true]],
        ];
    }

    public function collection()
    {
        $given_data = $this->given_data;

        $showroom_id = $given_data['showroom_id'];

        $direct_income = $given_data['direct_income_data']['leadger'];
        $direct_income_ids = $given_data['direct_income_data']['direct_income_leadgers'];

        $indirect_income = $given_data['indirect_income_data']['leadger'];
        $indirect_income_ids = array_diff($given_data['indirect_income_data']['direct_income_leadgers'],[Settings('income_summary_debit_leadger')]);

        $direct_expense = $given_data['direct_expense_data']['leadger'];
        $direct_expense_ids = $given_data['direct_expense_data']['direct_income_leadgers'];

        $indirect_expense = $given_data['indirect_expense_data']['leadger'];
        $indirect_expense_ids = $given_data['indirect_expense_data']['direct_income_leadgers'];

        if (($key = array_search(Settings('default_purchase_account'), $direct_expense_ids)) !== false) {
            unset($direct_expense_ids[$key]);
        }
        if (($key = array_search(Settings('company_income_tax'), $direct_expense_ids)) !== false) {
            unset($direct_expense_ids[$key]);
        }
        if (($key = array_search(Settings('company_income_tax'), $indirect_expense_ids)) !== false) {
            unset($indirect_expense_ids[$key]);
        }

        $all_financial_years = $given_data['all_financial_years'];

        $financial_years = $given_data['financial_years'];

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "Account Name";
        $x->col_2 = "Note";
        foreach ($financial_years as $financial_year) {
            $to_date = ($financial_year->end_date != null) ? date(Settings("date_format_id"), strtotime($financial_year->end_date)) : date(Settings("date_format_id"), strtotime(\Carbon\Carbon::now()->format('Y-m-d')));
            $x->col_3[] = date(Settings("date_format_id"), strtotime($financial_year->start_date)). " - " .$to_date;
        }
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = $direct_income->name;
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        foreach ($direct_income->childrenCategories as $child) {
            $x = new \stdClass();
            $x->col_1 = $child->name;
            $x->col_2 = ($child->is_cost_center == 0) ? $child->code : "";
            foreach ($financial_years as $financial_year) {
                $x->col_3[] = ($child->is_cost_center == 0) ? single_price($child->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
            }
            $new_array->push($x);
            if ($child->categories) {
                $new_array = $this->getChildren($child->categories, $financial_years, $new_array, $showroom_id);
            }
        }

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "Total";
        foreach ($financial_years as $financial_year) {
            $total_direct_income_val[$financial_year->id] = $financial_year->showTotalBalance($direct_income_ids, $showroom_id);
            $x->col_3[] = single_price($total_direct_income_val[$financial_year->id]);
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
        $x->col_1 = $direct_expense->name;
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        foreach ($direct_expense->childrenCategories->whereNotIn('id',[Settings('default_purchase_account')]) as $child) {
            $x = new \stdClass();
            $x->col_1 = $child->name;
            $x->col_2 = ($child->is_cost_center == 0) ? $child->code : "";
            foreach ($financial_years as $financial_year) {
                $x->col_3[] = ($child->is_cost_center == 0) ? single_price($child->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
            }
            $new_array->push($x);
            if ($child->categories) {
                $new_array = $this->getChildren($child->categories->whereNotIn('id',[Settings('default_purchase_account')]), $financial_years, $new_array, $showroom_id);
            }
        }

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "Total";
        foreach ($financial_years as $financial_year) {
            $total_direct_expense_val[$financial_year->id] = $financial_year->showTotalBalance($direct_expense_ids, $showroom_id);
            $x->col_3[] = single_price($total_direct_expense_val[$financial_year->id]);
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
        $x->col_1 = "";
        $x->col_2 = "Gross Profit or Loss";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($total_direct_income_val[$financial_year->id] - $total_direct_expense_val[$financial_year->id]);
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
        $x->col_1 = $indirect_income->name;
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        foreach ($indirect_income->childrenCategories as $child) {
            $x = new \stdClass();
            $x->col_1 = $child->name;
            $x->col_2 = ($child->is_cost_center == 0) ? $child->code : "";
            foreach ($financial_years as $financial_year) {
                $x->col_3[] = ($child->is_cost_center == 0) ? single_price($child->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
            }
            $new_array->push($x);
            if ($child->categories) {
                $new_array = $this->getChildren($child->categories, $financial_years, $new_array, $showroom_id);
            }
        }

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "Total";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($financial_year->showTotalBalance($indirect_income_ids, $showroom_id));
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
        $x->col_1 = $indirect_expense->name;
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        foreach ($indirect_expense->childrenCategories as $child) {
            $x = new \stdClass();
            $x->col_1 = $child->name;
            $x->col_2 = ($child->is_cost_center == 0) ? $child->code : "";
            foreach ($financial_years as $financial_year) {
                $x->col_3[] = ($child->is_cost_center == 0) ? single_price($child->FinancialYearBalance($financial_year->id, $showroom_id)) : "";
            }
            $new_array->push($x);
            if ($child->categories) {
                $new_array = $this->getChildren($child->categories, $financial_years, $new_array, $showroom_id);
            }
        }

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "Total";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($financial_year->showTotalBalance($indirect_expense_ids, $showroom_id));
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
        $x->col_1 = "";
        $x->col_2 = "";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = "";
        }
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "Profit and Loss Before Tax";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($financial_year->showTotalProfitLossBeforeTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id));
        }
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "Provision For Tax";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($financial_year->showTotalFinancialYearTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id));
        }
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "Net Profit Loss this Financial Year";
        foreach ($financial_years as $financial_year) {
            $x->col_3[] = single_price($financial_year->showNetTotalProfitLossFinancialYear($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id));
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
            'C' => 40,
            'D' => 45,
            'E' => 40,
            'F' => 40,
            'G' => 40,
            'H' => 40,
            'I' => 40,
            'J' => 40,
            'K' => 40,
            'L' => 40,
            'M' => 40,
            'N' => 40,
            'O' => 40,
            'P' => 40,
            'Q' => 40,
            'R' => 40,
            'S' => 40,
            'T' => 40,
            'U' => 40,
            'V' => 40,
            'W' => 40,
            'X' => 40,
            'Y' => 40,
            'Z' => 40,
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
                $new_array = $this->getChildren2($child_account->categories->whereNotIn('id',[Settings('default_purchase_account')]), $financial_years, $new_array, $showroom_id);
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
                $new_array = $this->getChildren3($child_account->categories->whereNotIn('id',[Settings('default_purchase_account')]), $financial_years, $new_array, $showroom_id);
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
                $new_array = $this->getChildren3($child_account->categories->whereNotIn('id',[Settings('default_purchase_account')]), $financial_years, $new_array, $showroom_id);
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
            if ($child_account->categories) {
                $new_array = $this->getChildren3($child_account->categories->whereNotIn('id',[Settings('default_purchase_account')]), $financial_years, $new_array, $showroom_id);
            }
        }
        return $new_array;
    }
}
