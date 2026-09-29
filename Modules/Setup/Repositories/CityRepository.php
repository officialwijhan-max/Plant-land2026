<?php

namespace Modules\Setup\Repositories;

use Modules\Setup\Entities\City;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setup\Exports\CityExport;
use Auth;

class CityRepository
{
    public function csvDownload()
    {
        if (file_exists(public_path("uploads/csv/city.xlsx"))) {
          unlink(public_path("uploads/csv/city.xlsx"));
        }
        return Excel::store(new CityExport, 'uploads/csv/city.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column)
    {
        $items = City::query();
        $items = $items->with('state');
        if ($quick_search != null) {
            $items = $items->whereLike(['name','state.name'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = City::count();

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

    public function listForSelect($search, $state_id)
    {
        $items = City::query();
        if ($search != '') {
            $items = $items->whereLike(['name'], $search);
        }

        $items = $items->where('state_id', $state_id);

        $items = $items->with('state')->paginate(10);

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
