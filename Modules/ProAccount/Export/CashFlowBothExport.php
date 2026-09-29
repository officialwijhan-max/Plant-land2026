<?php

namespace Modules\ProAccount\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Account\Entities\CashFlowDetail;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use DB;

class CashFlowBothExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings, WithEvents
{
    use Exportable;

    protected $cash_in, $cash_out, $date;

    function __construct($cash_in, $cash_out, $start_date, $end_date) {
        $this->cash_in = $cash_in;
        $this->cash_out = $cash_out;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function drawings()
   {
       $drawing = new Drawing();
       $drawing->setName(Settings("company_name"));
       $drawing->setPath(public_path('/frontend/img/logo.png'));
       $drawing->setHeight(60);
       $drawing->setCoordinates('A1');
       return $drawing;
   }

    public function collection()
    {
        $end_date_filter = $this->end_date;
        $start_date_filter = $this->start_date;
        $filter_cash_in = $this->cash_in;
        $filter_cash_out = $this->cash_out;
        $transactions_in = array();
        $transactions_out = array();
        if ($filter_cash_in == "cash-in") {
            $transactions_in = CashFlowDetail::whereBetween('created_at',array($start_date_filter." 00:00:00", $end_date_filter." 23:59:59"))
                                                ->whereHas('voucher', function($q){
                                                    $q->where('is_approve', 1);
                                                })->whereHas('cash_flow_account', function($r){
                                                    $r->where('type', 4);
                                                })->with(['cash_flow_account' => function($qr){
                                                    $qr->select('id','name','code');
                                                },
                                                'voucher' => function($que){
                                                    $que->select('id', 'date');
                                                }])
                                                ->get()->groupby('cash_flow_account_id');
        }
        if ($filter_cash_out == "cash-out") {
            $transactions_out = CashFlowDetail::whereBetween('created_at',array($start_date_filter." 00:00:00", $end_date_filter." 23:59:59"))
                                                ->whereHas('voucher', function($q){
                                                    $q->where('is_approve', 1);
                                                })->whereHas('cash_flow_account', function($r){
                                                    $r->where('type', 3);
                                                })->with(['cash_flow_account' => function($qr){
                                                    $qr->select('id','name','code');
                                                },
                                                'voucher' => function($que){
                                                    $que->select('id', 'date');
                                                }])
                                                ->get()->groupby('cash_flow_account_id');
        }
        $new_array = collect();
        $total_cash_in = 0;
        $total_cash_out = 0;
        $x = new \stdClass();
        $x->date = "Income";
        $x->leadger_name = "";
        $x->code = "";
        $x->amount = "";
        $x->_date = "Expense";
        $x->_leadger_name = "";
        $x->_code = "";
        $x->_amount = "";
        $new_array->push($x);
        $x = new \stdClass();
        $x->date = "From";
        $x->leadger_name = $start_date_filter;
        $x->code = "To";
        $x->amount = $end_date_filter;
        $x->_date = "From";
        $x->_leadger_name = $start_date_filter;
        $x->_code = "To";
        $x->_amount = $end_date_filter;
        $new_array->push($x);
        $x = new \stdClass();
        $x->date = "Date";
        $x->leadger_name = "Name";
        $x->code = "Code";
        $x->amount = "Amount";
        $x->_date = "Date";
        $x->_leadger_name = "Name";
        $x->_code = "Code";
        $x->_amount = "Amount";
        $new_array->push($x);
        foreach ($transactions_in as $key => $transaction) {
            $x = new \stdClass();
            $x->date = $transaction->first()->voucher->date;
            $x->leadger_name = $transaction->first()->cash_flow_account->name;
            $x->code = $transaction->first()->cash_flow_account->code;
            $x->amount = single_price($transaction->sum('amount'));
            $total_cash_in += $transaction->sum('amount');
            $x->_date = '';
            $x->_leadger_name = '';
            $x->_code = '';
            $x->_amount = '';
            $new_array->push($x);
        }
        foreach ($transactions_out as $key => $transaction_o) {
            $x = new \stdClass();
            $x->_date = $transaction_o->first()->voucher->date;
            $x->_leadger_name = $transaction_o->first()->cash_flow_account->name;
            $x->_code = $transaction_o->first()->cash_flow_account->code;
            $x->_amount = single_price($transaction_o->sum('amount'));
            $total_cash_out += $transaction_o->sum('amount');
            $x->date = '';
            $x->leadger_name = '';
            $x->code = '';
            $x->amount = '';
            $new_array->push($x);
        }
        $x = new \stdClass();
        $x->date = "Total In";
        $x->leadger_name = "";
        $x->code = "";
        $x->amount = single_price($total_cash_in);
        $x->_date = "Total Out";
        $x->_leadger_name = "";
        $x->_code = "";
        $x->_amount = single_price($total_cash_out);
        $new_array->push($x);

        return $new_array;
    }

    public function map($transactions): array
    {
        return [
            $transactions->date,
            $transactions->leadger_name,
            $transactions->code,
            $transactions->amount,
            $transactions->_date,
            $transactions->_leadger_name,
            $transactions->_code,
            $transactions->_amount,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 30,
            'D' => 25,
            'E' => 20,
            'F' => 30,
            'G' => 30,
            'H' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            6    => ['font' => ['bold' => true, 'size' => 12]],
            7    => ['font' => ['bold' => true, 'size' => 11]],

            // Styling an entire column.
            'D'  => ['font' => ['bold' => true]],

            // Styling an entire column.
            'H'  => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A6:G6')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $event->sheet->getDelegate()->mergeCells('A6:D6');
            $event->sheet->getDelegate()->mergeCells('E6:H6');
            },
        ];
    }
}
