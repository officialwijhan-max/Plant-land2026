<?php

namespace Modules\Setup\Repositories;

use Modules\Setup\Entities\State;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setup\Exports\StateExport;

class StateRepository
{
    public function listForSelect($search, $country_id)
    {
        $items = State::query();
        if ($search != '') {
            $items = $items->whereLike(['name'], $search);
        }
        $items = $items->where('country_id', $country_id);

        $items = $items->with('country')->paginate(10);

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

    public function csvDownload()
    {
        if (file_exists(public_path("uploads/csv/states.xlsx"))) {
          unlink(public_path("uploads/csv/states.xlsx"));
        }
        return Excel::store(new StateExport, 'uploads/csv/states.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column)
    {
        $items = State::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = State::count();

            if ($column != null) {
                return $items->with('country')->orderBy($column, $sort)->paginate($total_number);
            }else {
                return $items->with('country')->paginate($total_number);
            }
        }else {
            if ($column != null) {
                return $items->with('country')->orderBy($column, $sort)->paginate($row_count);
            }else {
                return $items->with('country')->paginate($row_count);
            }
        }
    }
}
