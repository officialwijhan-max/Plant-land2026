<?php

namespace Modules\Product\Repositories;

use Modules\Product\Entities\UnitType;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\UnitTypeExport;
use Modules\Product\Imports\UnitImport;
use Response;

class UnitTypeRepository implements UnitTypeRepositoryInterface
{
    public function all()
    {
        return UnitType::orderBy("id", "DESC")->get();
    }

    public function serachBased($search_keyword)
    {
        return UnitType::whereLike(['name', 'description'], $search_keyword)->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/unit-type-list.xlsx"))) {
          unlink(public_path("uploads/csv/unit-type-list.xlsx"));
        }
        return Excel::store(new UnitTypeExport($data), 'uploads/csv/unit-type-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column, $relational_data = [], $selected_data = ['*'])
    {
        $items = UnitType::query();
        $items = $items->with($relational_data);
        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = UnitType::count();

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

    public function listForSelect($request)
    {
        if ($request->search != '') {
            $items = UnitType::whereLike(['name'], $request->search)->where('status', 1)->paginate(10, ['id', 'name']);
        } else {
            $items = UnitType::where('status', 1)->paginate(10, ['id', 'name']);
        }


        $response = [];
        foreach($items as $item){
            $response[]  =[
                'id'    => $item->id,
                'text'  => $item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0)
        {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function create(array $data)
    {
        $variant = new UnitType();
        $variant->fill($data)->save();
        return $variant;
    }

    public function find($id)
    {
        return UnitType::findOrFail($id);
    }

    public function update(array $data, $id)
    {

        $variant = UnitType::findOrFail($id);
        $variant->update($data);
    }

    public function delete($id)
    {
        return UnitType::findOrFail($id)->delete();
    }

    public function csv_upload_unit($data)
    {
        if (!empty($data['file'])) {
            $fileName = time().'_'.$data['file']->getClientOriginalName();
            request()->file('file')->storeAs('reports', $fileName, 'public');

            Excel::import(new UnitImport, request()->file('file'));
        }
    }

    public function csv_download()
    {
        $table = UnitType::all();
        $filename = "units_tbl.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('id', 'name'));

        foreach($table as $row) {
            fputcsv($handle, array($row['id'], $row['name']));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::download($filename, 'units_tbl.csv', $headers);
    }
}
