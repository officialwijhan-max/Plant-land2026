<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\Voucher;
use Modules\ProAccount\Entities\Leadger;
use Maatwebsite\Excel\Facades\Excel;
use update\Modules\ProAccount\Exports\VoucherExport;

class VoucherRecieveRepository
{
    public function csvDownloadRcvVoucher()
    {
        if (file_exists(public_path("uploads/csv/voucher_recieves.xlsx"))) {
          unlink(public_path("uploads/csv/voucher_recieves.xlsx"));
        }
        return Excel::store(new VoucherExport("voucher_recieves"), 'uploads/csv/voucher_recieves.xlsx', 'public_folder');
    }

    public function withPaginateRcvVoucher($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = Voucher::query();
        $items = $items->with($relational_data)->whereIn('type', ['rec_cash', 'rec_bank'])->where('showroom_id', session()->get('showroom_id'));
        if ($quick_search != null) {
            $items = $items->whereLike(['txn_id','narration','date','amount'], $quick_search)
                            ->whereIn('type', ['rec_cash', 'rec_bank']);
        }
        if ($row_count == "all") {
            $total_number = Voucher::whereIn('type', ['rec_cash', 'rec_bank'])->count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function getAccountByCashBankOthers($type)
    {
        return Leadger::where('acc_type', $type)
                        ->where('is_active', 1)
                        ->get(['id','name']);
    }

    public function voucherDetails(array $data)
    {
        return Voucher::with('transactions','transactions.leadger','transactions.subLeadger')
            ->findOrFail($data['id']);
    }
}
