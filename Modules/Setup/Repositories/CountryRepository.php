<?php

namespace Modules\Setup\Repositories;

use Modules\Setup\Entities\Country;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setup\Exports\CountryExport;
use Modules\Setup\Repositories\CountryRepositoryInterface;

class CountryRepository implements CountryRepositoryInterface
{
    public function csvDownload()
    {
        if (file_exists(public_path("uploads/csv/country.xlsx"))) {
          unlink(public_path("uploads/csv/country.xlsx"));
        }
        return Excel::store(new CountryExport, 'uploads/csv/country.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column)
    {
        $items = Country::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Country::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number);
            }else {
                return $items->paginate($total_number);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count);
            }else {
                return $items->paginate($row_count);
            }
        }
    }

    public function listForSelect($search)
    {
        $items = Country::query();
        if ($search != '') {
            $items = $items->whereLike(['name'], $search);
        }

        $items = $items->paginate(10);

        $response = [];
        foreach ($items as $item) {
            $response[]  = [
                'id'    => $item->id,
                'text'  => $item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }
}
