<?php

namespace Modules\ProAccount\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\ProAccount\Repositories\LeadgerRepository;
use update\Modules\ProAccount\Repositories\LeadgerReportRepository;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class LeadgerReportExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings, WithEvents
{
    use Exportable;

    protected  $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $req_data = $this->data;


        $total_balance = 0;

        $new_array = collect();

        if (isset($req_data['dateFrom'])) {
            $date_range = date('m.d.Y', strtotime($req_data['dateFrom'])) ." to ". date('m.d.Y', strtotime($req_data['dateTo']));
        } else {
            $date_range = "";
        }

        $x = new \stdClass();
        $x->col_1 = $req_data['leadgerAccount']->name." - (". $req_data['leadgerAccount']->code.") Report ".$date_range;
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'DATE';
        $x->col_2 = 'TXN ID';
        $x->col_3 = 'NARRATION';
        $x->col_4 = 'DEBIT';
        $x->col_5 = 'CREDIT';
        $x->col_6 = 'BALANCE';
        $new_array->push($x);

        if ($req_data['account_type'] == 1 || $req_data['account_type'] == 3) {
            $currentBalance = 0 + $req_data['balance'];
            $total_dr = 0;
            $total_cr = 0;
            if ($req_data['balance'] != 0) {
                $x = new \stdClass();
                $x->col_1 = 'Balance Forwarded';
                $x->col_2 = '';
                $x->col_3 = '';
                $x->col_4 = '';
                $x->col_5 = '';
                $x->col_6 = number_format($currentBalance, 2);
                $new_array->push($x);
            }

            $opening_cr_total = $req_data['transactions']->where('is_opening', 1)->where('type', "Cr")->sum('amount');
            $opening_dr_total = $req_data['transactions']->where('is_opening', 1)->where('type', "Dr")->sum('amount');
            $current_opening = $opening_dr_total - $opening_cr_total;
            $currentBalance = $currentBalance + ($opening_dr_total - $opening_cr_total);

            if ($current_opening > 0) {
                $x = new \stdClass();
                $x->col_1 = 'Opening Balance';
                $x->col_2 = '';
                $x->col_3 = '';
                $x->col_4 = '';
                $x->col_5 = '';
                $x->col_6 = number_format($current_opening, 2);
                $new_array->push($x);
            }

            foreach ($req_data['transactions']->where('is_opening','!=', 1)->groupBy('voucher_id') as $transaction) {
                $currentBalance = $transaction->where('type', 'Dr')->count() > 0 ? ($currentBalance + $transaction->where('type', 'Dr')->sum('amount')) :  ($currentBalance - $transaction->where('type', 'Cr')->sum('amount'));

                $x = new \stdClass();
                $x->col_1 = @$transaction->first()->voucher->date;
                $x->col_2 = @$transaction->first()->voucher->GetTypeName()." - ".@$transaction->first()->voucher->txn_id;
                $x->col_3 = $transaction->first()->narration;
                if ($transaction->where('type', 'Dr')->count() > 0) {
                    $total_dr += $transaction->where('type', 'Dr')->sum('amount');
                    $x->col_4 = number_format($transaction->where('type', 'Dr')->sum('amount'), 2);
                }else {
                    $x->col_4 = '';
                }
                if ($transaction->where('type', 'Cr')->count() > 0) {
                    $total_cr += $transaction->where('type', 'Cr')->sum('amount');
                    $x->col_5 = number_format($transaction->where('type', 'Cr')->sum('amount'), 2);
                }else {
                    $x->col_5 = '';
                }
                $x->col_6 =number_format($currentBalance, 2);
                $new_array->push($x);
            }
        } else {
            $currentBalance = 0 + $req_data['balance'];
            $total_dr = 0;
            $total_cr = 0;
            if ($req_data['balance'] != 0) {
                $x = new \stdClass();
                $x->col_1 = 'Balance Forwarded';
                $x->col_2 = '';
                $x->col_3 = '';
                $x->col_4 = '';
                $x->col_5 = '';
                $x->col_6 = number_format($currentBalance, 2);
                $new_array->push($x);
            }

            $opening_cr_total = $req_data['transactions']->where('is_opening', 1)->where('type', "Cr")->sum('amount');
            $opening_dr_total = $req_data['transactions']->where('is_opening', 1)->where('type', "Dr")->sum('amount');
            $current_opening = $opening_cr_total - $opening_dr_total;
            $currentBalance = $currentBalance + ($opening_cr_total - $opening_dr_total);

            if ($current_opening > 0) {
                $x = new \stdClass();
                $x->col_1 = 'Opening Balance';
                $x->col_2 = '';
                $x->col_3 = '';
                $x->col_4 = '';
                $x->col_5 = '';
                $x->col_6 = number_format($current_opening, 2);
                $new_array->push($x);
            }

            foreach ($req_data['transactions']->where('is_opening','!=', 1)->groupBy('voucher_id') as $transaction) {
                $currentBalance = $transaction->where('type', 'Cr')->count() > 0 ? ($currentBalance + $transaction->where('type', 'Cr')->sum('amount')) :  ($currentBalance - $transaction->where('type', 'Dr')->sum('amount'));

                $x = new \stdClass();
                $x->col_1 = @$transaction->first()->voucher->date;
                $x->col_2 = @$transaction->first()->voucher->GetTypeName()." - ".@$transaction->first()->voucher->txn_id;
                $x->col_3 = $transaction->first()->narration;
                if ($transaction->where('type', 'Dr')->count() > 0) {
                    $total_dr += $transaction->where('type', 'Dr')->sum('amount');
                    $x->col_4 = number_format($transaction->where('type', 'Dr')->sum('amount'), 2);
                }else {
                    $x->col_4 = '';
                }
                if ($transaction->where('type', 'Cr')->count() > 0) {
                    $total_cr += $transaction->where('type', 'Cr')->sum('amount');
                    $x->col_5 = number_format($transaction->where('type', 'Cr')->sum('amount'), 2);
                }else {
                    $x->col_5 = '';
                }
                $x->col_6 =number_format($currentBalance, 2);
                $new_array->push($x);
            }
        }

        $x = new \stdClass();
        $x->col_1 = '';
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = '';
        $x->col_5 = '';
        $x->col_6 = '';
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = 'GRAND TOTAL';
        $x->col_2 = '';
        $x->col_3 = '';
        $x->col_4 = number_format($total_dr, 2);
        $x->col_5 = number_format($total_cr, 2);
        $x->col_6 = number_format($currentBalance, 2);
        $new_array->push($x);

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
        ];
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function drawings()
   {
       $drawing = new Drawing();
       $drawing->setName(Settings("company_name"));
       $drawing->setPath(public_path('/frontend/img/logo.png'));
       $drawing->setHeight(70);
       $drawing->setCoordinates('A3');
       return $drawing;
   }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 60,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            8    => ['font' => ['bold' => true, 'size' => 11]],
            9    => ['font' => ['bold' => true, 'size' => 10]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A8:F8')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('D:D')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getDelegate()->getStyle('E:E')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getDelegate()->getStyle('F:F')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getDelegate()->mergeCells('A8:F8');
            },
        ];
    }
}
