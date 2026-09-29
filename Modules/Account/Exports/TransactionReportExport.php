<?php

namespace Modules\Account\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class TransactionReportExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;
    protected $filter_account, $transactions, $accont_type, $dateFrom, $dateTo, $account_id, $balance, $beforedateAccount, $opening_balance;
    function __construct($filter_account, $transactions, $accont_type, $dateFrom, $dateTo, $account_id, $balance, $beforedateAccount, $opening_balance) {
        $this->filter_account = $filter_account;
        $this->transactions = $transactions;
        $this->accont_type = $accont_type;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->account_id = $account_id;
        $this->balance = $balance;
        $this->beforedateAccount = $beforedateAccount;
        $this->opening_balance = $opening_balance;
    }

    public function collection()
    {
        $filter_account = $this->filter_account;
        $transactions = $this->transactions;
        $accont_type = $this->accont_type;
        $dateFrom = $this->dateFrom;
        $dateTo = $this->dateTo;
        $account_id = $this->account_id;
        $balance = $this->balance;
        $beforedateAccount = $this->beforedateAccount;
        $opening_balance = $this->opening_balance;

        $new_array = collect();
        
        $x = new \stdClass();
        $x->col_1 = "TRANSACTION REPORT : ".($filter_account) ? $filter_account->name : '';
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "";
        $x->col_5 = "";
        $x->col_6 = "";
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'DATE';
        $x->col_2 = 'REFERENCE NO.';
        $x->col_3 = 'DESCRIPTION';
        $x->col_4 = 'DEBIT';
        $x->col_5 = 'CREDIT';
        $x->col_6 = 'BALANCE';
        $new_array->push($x);

        $currentBalance = 0 + $balance + $opening_balance;

        if ($accont_type == 1 || $accont_type == 3)
        {
            $x = new \stdClass();
            $x->col_1 = __('account.Opening Balance');
            $x->col_2 = '';
            $x->col_3 = '';
            $x->col_4 = '';
            $x->col_5 = '';
            $x->col_6 = number_format($currentBalance, 2);
            $new_array->push($x);

            foreach ($transactions->sort() as $key => $payment) {
                if ($payment->type != "Dr") {
                    $currentBalance -= $payment->amount;
                } else {
                    $currentBalance += $payment->amount;
                }
                $x = new \stdClass();
                $x->col_1 = $payment->voucherable->date;
                $x->col_2 = (@$payment->voucherable->referable->invoice_no) ? @$payment->voucherable->referable->invoice_no : @$payment->voucherable->tx_id;
                $x->col_3 = @$payment->voucherable->narration;
                $x->col_4 = $payment->type == "Dr" ? number_format($payment->amount, 2) : '';
                $x->col_5 = $payment->type == "Cr" ? number_format($payment->amount, 2) : '';
                $x->col_6 = number_format($currentBalance, 2);
                $new_array->push($x);
            }

        } else {
            $x = new \stdClass();
            $x->col_1 = __('account.Opening Balance');
            $x->col_2 = '';
            $x->col_3 = '';
            $x->col_4 = '';
            $x->col_5 = '';
            $x->col_6 = number_format($currentBalance, 2);
            $new_array->push($x);

            foreach ($transactions->sort() as $key => $payment) {
                if ($payment->type != "Cr") {
                    $currentBalance -= $payment->amount;
                } else {
                    $currentBalance += $payment->amount;
                }
                $x = new \stdClass();
                $x->col_1 = $payment->voucherable->date;
                $x->col_2 = (@$payment->voucherable->referable->invoice_no) ? @$payment->voucherable->referable->invoice_no : @$payment->voucherable->tx_id;
                $x->col_3 = @$payment->voucherable->narration;
                $x->col_4 = $payment->type == "Dr" ? number_format($payment->amount, 2) : '';
                $x->col_5 = $payment->type == "Cr" ? number_format($payment->amount, 2) : '';
                $x->col_6 = number_format($currentBalance, 2);
                $new_array->push($x);
            }
        }

    
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
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 50,
            'D' => 25,
            'E' => 20,
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 11]],
            2    => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:F1')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A1:F1');
            },
        ];
    }
}
