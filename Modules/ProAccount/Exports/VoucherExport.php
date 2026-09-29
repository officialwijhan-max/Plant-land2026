<?php

namespace Modules\ProAccount\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\ProAccount\Entities\Voucher;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Modules\Employee\Repositories\Attendance\AttendanceRepository;
use Carbon\Carbon;

class VoucherExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings
{
    use Exportable;

    protected $type;

    function __construct($type) {
        $this->type = $type;
    }

    public function collection()
    {
        $type_voucher = $this->type;
        $items = Voucher::query();
        if ($type_voucher == "voucher_recieves") {
            $items = $items->whereIn('type', ['rec_cash', 'rec_bank']);
        }
        if ($type_voucher == "voucher_payments") {
            $items = $items->whereIn('type', ['pay_cash', 'pay_bank', 'pay_in_due']);
        }
        if ($type_voucher == "sales") {
            $items = $items->where('sale_or_purchase', 's');
        }
        if ($type_voucher == "purchase") {
            $items = $items->where('sale_or_purchase', 'p');
        }
        if ($type_voucher == "expense") {
            $items = $items->where('sale_or_purchase', 'exp');
        }
        if ($type_voucher == "income") {
            $items = $items->where('sale_or_purchase', 'inc');
        }
        if ($type_voucher == "opening") {
            $items = $items->where('sale_or_purchase', 'opening');
        }

        $items = $items->latest()->select('id','amount', 'date','narration','txn_id','type','is_approve')->get();

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = null;
            if ($type_voucher == "voucher_recieves") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'voucher_recieve_list')->first();
            }
            if ($type_voucher == "voucher_payments") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'voucher_payment_list')->first();
            }
            if ($type_voucher == "sales") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'account_sales_list')->first();
            }
            if ($type_voucher == "purchase") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'account_purchase_list')->first();
            }
            if ($type_voucher == "expense") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'expenses_list')->first();
            }
            if ($type_voucher == "income") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'income_list')->first();
            }
            if ($type_voucher == "opening") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'opening_balance_list')->first();
            }
        }else {
            $permissions = null;
        }

        $new_array = collect();
        $datas = array();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Date';
        $x->col_3 = 'Txn Id';
        $x->col_4 = 'Reference NO';
        $x->col_5 = 'Amount';
        $x->col_6 = 'Approved';

        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_2 = $item->date;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'txn_id')) {
                    $x->col_3 = $item->txn_id;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'reference_no')) {
                    $x->col_4 = $item->narration;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'amount')) {
                    $x->col_5 = single_price($item->amount);
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'approved')) {
                    if ($item->is_approve == 0)
                        $x->col_6 = trans("common.Pending");
                    elseif ($item->is_approve == 1)
                        $x->col_6 = trans("common.Approved");
                    else
                        $x->col_6 = trans("common.Cancelled");
                }else {
                    $x->col_6 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->date;
                $x->col_3 = $item->txn_id;
                $x->col_4 = $item->narration;
                $x->col_5 = single_price($item->amount);
                if ($item->is_approve == 0)
                    $x->col_6 = trans("common.Pending");
                elseif ($item->is_approve == 1)
                    $x->col_6 = trans("common.Approved");
                else
                    $x->col_6 = trans("common.Cancelled");

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
            'A' => 10,
            'B' => 25,
            'C' => 25,
            'D' => 20,
            'E' => 20,
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            8    => ['font' => ['bold' => true]]
        ];
    }
}
