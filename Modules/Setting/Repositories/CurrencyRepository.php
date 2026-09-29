<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Model\Currency;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setting\Exports\CurrencyExport;
use Modules\Setting\Repositories\CurrencyRepositoryInterface;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    public function all()
    {
        return Currency::latest()->get();
    }

    public function serachBased($search_keyword)
    {
        return Currency::whereLike(['name', 'symbol', 'code'], $search_keyword)->latest()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/currencies.xlsx"))) {
          unlink(public_path("uploads/csv/currencies.xlsx"));
        }
        return Excel::store(new CurrencyExport($data), 'uploads/csv/currencies.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column)
    {
        $items = Currency::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['name','code'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Currency::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number);
            }else {
                return $items->latest()->paginate($total_number);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count);
            }else {
                return $items->latest()->paginate($row_count);
            }
        }
    }

    public function create(array $data)
    {
        $currency = new Currency();
        $currency->fill($data)->save();
    }

    public function find($id)
    {
        return Currency::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        return Currency::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        return Currency::findOrFail($id)->delete();
    }
}
