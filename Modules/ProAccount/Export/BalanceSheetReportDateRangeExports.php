<?php

namespace Modules\ProAccount\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class BalanceSheetReportDateRangeExports implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
{
    use Exportable;

    protected $data;

    function __construct($data) {
        $this->data = $data;
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
        $given_data = $this->data;


        $total_direct_income = 0;
        $total_direct_expense = 0;

        $assets = $given_data['assets'];

        $liabilities = $given_data['liabilities'];

        $dateFrom = $given_data['dateFrom'];
        $dateTo = $given_data['dateTo'];

        $showroom_id = $given_data['showroom_id'];


        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "Account Name";
        $x->col_2 = "Note";
        $x->col_3 = date('Y-m-d', strtotime($dateFrom)). " - " .date('Y-m-d', strtotime($dateTo));
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "Assets type Account";
        $x->col_2 = "";
        $x->col_3 = "";
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        $x->col_3 = "";
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        $x->col_3 = "";
        $new_array->push($x);

        foreach ($assets as $key => $asset) {
            if ($asset->parent_id != 0) {
                $x = new \stdClass();
                $x->col_1 = $asset->name;
                $x->col_2 = "";
                $x->col_3 = "";
                $new_array->push($x);
            }

            foreach ($asset->childrenCategories as $key => $leadger_a) {
                $total_direct_income += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                foreach ($leadger_a->childrenCategories as $key => $leadger_b) {
                    $total_direct_income += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                    foreach ($leadger_b->childrenCategories as $key => $leadger_c) {
                        $total_direct_income += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                        foreach ($leadger_c->childrenCategories as $key => $leadger_d) {
                            $total_direct_income += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                            foreach ($leadger_d->childrenCategories as $key => $leadger_e) {
                                $total_direct_income += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                foreach ($leadger_e->childrenCategories as $key => $leadger_f) {
                                    $total_direct_income += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                }
                            }
                        }
                    }
                }
            }
            foreach ($asset->childrenCategories as $child) {
                if ($child->BalanceAmountBetweenDate($dateFrom, $dateTo) != 0) {
                    $x = new \stdClass();
                    $x->col_1 = $child->name;
                    $x->col_2 = ($child->is_cost_center == 0) ? $child->code : "";
                    $x->col_3 = ($child->is_cost_center == 0) ? number_format($child->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id), 2) : "";
                    $new_array->push($x);
                }
                if ($child->categories) {
                    $new_array = $this->getChildren($child->categories, $dateFrom, $dateTo, $new_array);
                }
            }

            $x = new \stdClass();
            $x->col_1 = "";
            $x->col_2 = "";
            $x->col_3 = "";
            $new_array->push($x);
        }

        $x = new \stdClass();
        $x->col_1 = "Total Assets";
        $x->col_2 = "";
        $x->col_3 = number_format($total_direct_income, 2);
        $new_array->push($x);


        for ($i=0; $i < 2 ; $i++) {
            $x = new \stdClass();
            $x->col_1 = "";
            $x->col_2 = "";
            $x->col_3 = "";
            $new_array->push($x);
        }

        $x = new \stdClass();
        $x->col_1 = "Liabilities & Equities Accounts";
        $x->col_2 = "";
        $x->col_3 = "";
        $new_array->push($x);


        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        $x->col_3 = "";
        $new_array->push($x);

        foreach ($liabilities as $key => $liability) {
            if ($liability->parent_id != 0) {
                $x = new \stdClass();
                $x->col_1 = $liability->name;
                $x->col_2 = "";
                $x->col_3 = "";
                $new_array->push($x);
            }

            foreach ($liability->childrenCategories->whereNotIn('id', Settings('retail_earning_leadger')) as $key => $leadger_a) {
                $total_direct_expense += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                foreach ($leadger_a->childrenCategories->whereNotIn('id', Settings('retail_earning_leadger')) as $key => $leadger_b) {
                    $total_direct_expense += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                    foreach ($leadger_b->childrenCategories->whereNotIn('id', Settings('retail_earning_leadger')) as $key => $leadger_c) {
                        $total_direct_expense += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                        foreach ($leadger_c->childrenCategories->whereNotIn('id', Settings('retail_earning_leadger')) as $key => $leadger_d) {
                            $total_direct_expense += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                            foreach ($leadger_d->childrenCategories->whereNotIn('id', Settings('retail_earning_leadger')) as $key => $leadger_e) {
                                $total_direct_expense += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                foreach ($leadger_e->childrenCategories->whereNotIn('id', Settings('retail_earning_leadger')) as $key => $leadger_f) {
                                    $total_direct_expense += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                }
                            }
                        }
                    }
                }
            }
            foreach ($liability->childrenCategories as $child) {
                if ($child->BalanceAmountBetweenDate($dateFrom, $dateTo) != 0) {
                    $x = new \stdClass();
                    $x->col_1 = $child->name;
                    $x->col_2 = ($child->is_cost_center == 0) ? $child->code : "";
                    $x->col_3 = ($child->is_cost_center == 0) ? number_format($child->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id), 2) : "";
                    $new_array->push($x);
                }
                if ($child->categories) {
                    $new_array = $this->getChildren($child->categories->whereNotIn('id', Settings('retail_earning_leadger')), $dateFrom, $dateTo, $new_array);
                }
            }
            $x = new \stdClass();
            $x->col_1 = "";
            $x->col_2 = "";
            $x->col_3 = "";
            $new_array->push($x);
        }

        $x = new \stdClass();
        $x->col_1 = "Total Liabilities and Equity";
        $x->col_2 = "";
        $x->col_3 = number_format($total_direct_expense, 2);
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "Approximate Profit Loss";
        $x->col_2 = "";
        $x->col_3 = number_format($total_direct_income - $total_direct_expense, 2);
        $new_array->push($x);

        return $new_array;
    }

    public function map($row): array
    {
        $data = [];
        array_push($data, $row->col_1);
        array_push($data, $row->col_2);
        array_push($data, $row->col_3);
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

    public function getChildren($childrens, $dateFrom, $dateTo, $new_array, $showroom_id)
    {
        foreach ($childrens as $child_account) {
            if (count($child_account->transactionsForFinancialYear()) > 0) {
                $x = new \stdClass();
                $x->col_1 = $child_account->name;
                $x->col_2 = ($child_account->is_cost_center == 0) ? $child_account->code : "";
                $x->col_3 = ($child_account->is_cost_center == 0) ? number_format($child_account->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id), 2) : "";
                $new_array->push($x);
            }
            if ($child_account->categories) {
                if ($child_account->type == 2) {
                    $new_array = $this->getChildren2($child_account->categories->whereNotIn('id', Settings('retail_earning_leadger')), $dateFrom, $dateTo, $new_array, $showroom_id);
                } else {
                    $new_array = $this->getChildren2($child_account->categories, $dateFrom, $dateTo, $new_array, $showroom_id);
                }
            }
        }
        return $new_array;
    }

    public function getChildren2($childrens, $dateFrom, $dateTo, $new_array, $showroom_id)
    {
        foreach ($childrens as $child_account) {
            if (count($child_account->transactionsForFinancialYear()) > 0) {
                $x = new \stdClass();
                $x->col_1 = $child_account->name;
                $x->col_2 = ($child_account->is_cost_center == 0) ? $child_account->code : "";
                $x->col_3 = ($child_account->is_cost_center == 0) ? number_format($child_account->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id), 2) : "";
                $new_array->push($x);
            }
            if ($child_account->categories) {
                if ($child_account->type == 2) {
                    $new_array = $this->getChildren3($child_account->categories->whereNotIn('id', Settings('retail_earning_leadger')), $dateFrom, $dateTo, $new_array, $showroom_id);
                } else {
                    $new_array = $this->getChildren3($child_account->categories, $dateFrom, $dateTo, $new_array, $showroom_id);
                }
            }
        }
        return $new_array;
    }

    public function getChildren3($childrens, $dateFrom, $dateTo, $new_array, $showroom_id)
    {
        foreach ($childrens as $child_account) {
            if (count($child_account->transactionsForFinancialYear()) > 0) {
                $x = new \stdClass();
                $x->col_1 = $child_account->name;
                $x->col_2 = ($child_account->is_cost_center == 0) ? $child_account->code : "";
                $x->col_3 = ($child_account->is_cost_center == 0) ? number_format($child_account->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id), 2) : "";
                $new_array->push($x);
            }
            if ($child_account->categories) {
                if ($child_account->type == 2) {
                    $new_array = $this->getChildren4($child_account->categories->whereNotIn('id', Settings('retail_earning_leadger')), $dateFrom, $dateTo, $new_array, $showroom_id);
                } else {
                    $new_array = $this->getChildren4($child_account->categories, $dateFrom, $dateTo, $new_array, $showroom_id);
                }
            }
        }
        return $new_array;
    }

    public function getChildren4($childrens, $dateFrom, $dateTo, $new_array, $showroom_id)
    {
        foreach ($childrens as $child_account) {
            if (count($child_account->transactionsForFinancialYear()) > 0) {
                $x = new \stdClass();
                $x->col_1 = $child_account->name;
                $x->col_2 = ($child_account->is_cost_center == 0) ? $child_account->code : "";
                $x->col_3 = ($child_account->is_cost_center == 0) ? number_format($child_account->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id), 2) : "";
                $new_array->push($x);
            }
            if ($child_account->categories) {
                if ($child_account->type == 2) {
                    $new_array = $this->getChildren3($child_account->categories->whereNotIn('id', Settings('retail_earning_leadger')), $dateFrom, $dateTo, $new_array, $showroom_id);
                } else {
                    $new_array = $this->getChildren3($child_account->categories, $dateFrom, $dateTo, $new_array, $showroom_id);
                }
            }
        }
        return $new_array;
    }
}
