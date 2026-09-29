<?php

namespace Modules\ProAccount\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\ProAccount\Entities\Leadger;
use Modules\ProAccount\Entities\Transaction;
use DB;

class TrialBalanceExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;

    protected $start_date, $end_date, $showroom_id;

    function __construct($start_date, $end_date, $showroom_id) {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->showroom_id = $showroom_id;
    }

    public function collection()
    {
        $end_date_filter = $this->end_date;
        $start_date_filter = $this->start_date;
        $showroom_id_filter = $this->showroom_id;

        $total_debit_balance_initial = 0;
        $total_credit_balance_initial = 0;
        $total_debit_balance = 0;
        $total_credit_balance = 0;
        $totalDebit = 0;
        $totalCrebit = 0;

        $leadgers = Leadger::where('is_cost_center', 0)->whereHas('transactions')
                            ->with(['transactions' => function($q){
                                $q->select('type','id','amount','leadger_id','date','amount','is_opening','is_approve','showroom_id');
                            }])->select('id','name','code','type')->get();

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "MOVEMENT TRIAL BALANCE";
        $x->col_5 = "";
        $x->col_6 = "";
        $x->col_7 = "";
        $new_array->push($x);
        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "INITIAL";
        $x->col_3 = "";
        $x->col_4 = ($start_date_filter != null  && $end_date_filter != null) ? $start_date_filter.' - '.$end_date_filter : "";
        $x->col_5 = "";
        $x->col_6 = "BALANCE";
        $x->col_7 = "";
        $new_array->push($x);
        $x = new \stdClass();
        $x->col_1 = "Ledger";
        $x->col_2 = "Ini. Debit";
        $x->col_3 = "Ini. Credit";
        $x->col_4 = "Mov. Debit";
        $x->col_5 = "Mov. Credit";
        $x->col_6 = "Debit";
        $x->col_7 = "Credit";
        $new_array->push($x);
        foreach ($leadgers as $key => $leadger)
        {
            $initial_debit = ($leadger->type == 1 || $leadger->type == 3) ? $leadger->BalanceAmountTillDate($start_date_filter, $showroom_id_filter) : 0;
            $initial_credit = ($leadger->type == 2 || $leadger->type == 4) ? $leadger->BalanceAmountTillDate($start_date_filter, $showroom_id_filter) : 0;
            $current_debit = ($leadger->type == 1 || $leadger->type == 3) ? $leadger->BalanceAmountBetweenDate($start_date_filter, $end_date_filter, $showroom_id_filter) : 0;
            $current_credit = ($leadger->type == 2 || $leadger->type == 4) ? $leadger->BalanceAmountBetweenDate($start_date_filter, $end_date_filter, $showroom_id_filter) : 0;

            $x = new \stdClass();
            $x->col_1 = $leadger->code.' '.$leadger->name;

            $x->col_2 = "";
            $x->col_3 = "";

            if (($leadger->type == 1 || $leadger->type == 3) && $initial_debit >= 0) {
                $x->col_2 = ($initial_debit > 0) ? number_format($initial_debit, 2) : "-";
                $total_debit_balance_initial += $initial_debit;
            }
            if (($leadger->type == 1 || $leadger->type == 3) && $initial_debit < 0) {
                $x->col_3 = abs($initial_debit);
                $total_credit_balance_initial += abs($initial_debit);
            }
            if (($leadger->type == 2 || $leadger->type == 4) && $initial_credit < 0) {
                $x->col_2 = abs($initial_credit);
                $total_debit_balance_initial += abs($initial_credit);
            }
            if (($leadger->type == 2 || $leadger->type == 4) && $initial_credit >= 0) {
                $x->col_3 = ($initial_credit > 0) ? number_format($initial_credit, 2) : "-";
                $total_credit_balance_initial += $initial_credit;
            }
            $x->col_4 = $leadger->DebitBalanceAmountBetweenDate($start_date_filter, $end_date_filter, $showroom_id_filter);
            $x->col_5 = $leadger->CreditBalanceAmountBetweenDate($start_date_filter, $end_date_filter, $showroom_id_filter);
            
            if (($leadger->type == 1 || $leadger->type == 3)  && $current_debit >= 0) {
                $total_debit_balance += $current_debit;
            }
            if (($leadger->type == 1 || $leadger->type == 3)  && $current_debit < 0) {
                $total_credit_balance += abs($current_debit);
            }

            if ((($leadger->type == 2 || $leadger->type == 4) && $current_credit < 0)) {
                $total_debit_balance += abs($current_credit);
            }
            if (($leadger->type == 2 || $leadger->type == 4) && $current_credit >= 0) {
                $total_credit_balance += $current_credit;
            }
            
            $sum_debit = $initial_debit + $current_debit;
            $sum_credit = $initial_credit + $current_credit;
            $x->col_6 = 0;
            $x->col_7 = 0;
            
            if (($leadger->type == 1 || $leadger->type == 3) && $sum_debit >= 0) {
                $x->col_6 = $sum_debit;
                $totalDebit += $sum_debit;
            }
            if (($leadger->type == 2 || $leadger->type == 4) && $sum_credit < 0) {
                $x->col_6 = abs($sum_credit);
                $totalDebit += abs($sum_credit);
            }
            if (($leadger->type == 1 || $leadger->type == 3)  && $sum_debit < 0) {
                $x->col_7 = abs($sum_debit);
                $totalCrebit += abs($sum_debit);
            }
            if (($leadger->type == 2 || $leadger->type == 4) && $sum_credit >= 0) {
                $x->col_7 = $sum_credit;
                $totalCrebit += $sum_credit;
            }

            $new_array->push($x);
        }
        $x = new \stdClass();
        $x->col_1 = "Total";
        $x->col_2 = single_price($total_debit_balance_initial);
        $x->col_3 = single_price($total_credit_balance_initial);
        $x->col_4 = single_price($total_debit_balance);
        $x->col_5 = single_price($total_credit_balance);
        $x->col_6 = single_price($totalDebit);
        $x->col_7 = single_price($totalCrebit);
        $new_array->push($x);
        return $new_array;
    }

    public function map($transactions): array
    {
        return [
            $transactions->col_1,
            $transactions->col_2,
            $transactions->col_3,
            $transactions->col_4,
            $transactions->col_5,
            $transactions->col_6,
            $transactions->col_7,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 60,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
            'G' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true, 'size' => 13]],
            3    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A3:G3')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $event->sheet->getDelegate()->mergeCells('D1:G1');
            $event->sheet->getDelegate()->mergeCells('B2:C2');
            $event->sheet->getDelegate()->mergeCells('D2:E2');
            $event->sheet->getDelegate()->mergeCells('F2:G2');
   
            },
        ];
    }
}
