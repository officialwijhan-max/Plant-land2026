<?php

namespace Modules\Product\Repositories;

use Modules\Product\Entities\ModelType;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\ModelExport;
use Modules\Product\Imports\ModelTypeImport;
use Response;

class ModelTypeRepository implements ModelTypeRepositoryInterface
{
    public function all()
    {
        return ModelType::orderBy("id", "DESC")->get();
    }

    public function forSelectIdName()
    {
        return ModelType::orderBy('name','asc')->select('name','id')->get();
    }

    public function serachBased($search_keyword)
    {
        return ModelType::whereLike(['name', 'description'], $search_keyword)->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/model-list.xlsx"))) {
          unlink(public_path("uploads/csv/model-list.xlsx"));
        }
        return Excel::store(new ModelExport($data), 'uploads/csv/model-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column, $relational_data = [], $selected_data = ['*'])
    {
        $items = ModelType::query();
        $items = $items->with($relational_data);
        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = ModelType::count();

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
            $items = ModelType::whereLike(['name'], $request->search)->where('status', 1)->paginate(10, ['id', 'name']);
        } else {
            $items = ModelType::where('status', 1)->paginate(10, ['id', 'name']);
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
        $variant = new ModelType();
        $variant->fill($data)->save();
        return $variant;
    }

    public function find($id)
    {
        return ModelType::findOrFail($id);
    }

    public function update(array $data, $id)
    {

        $variant = ModelType::findOrFail($id);
        $variant->update($data);
    }

    public function delete($id)
    {
        return ModelType::findOrFail($id)->delete();
    }
    public function csv_upload_model_type($data)
    {
        if (!empty($data['file'])) {
            $fileName = time().'_'.$data['file']->getClientOriginalName();
            request()->file('file')->storeAs('reports', $fileName, 'public');

            Excel::import(new ModelTypeImport, request()->file('file'));
        }
    }

    public function csv_download()
    {
        $table = ModelType::all();
        $filename = "models.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('id', 'name'));

        foreach($table as $row) {
            fputcsv($handle, array($row['id'], $row['name']));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::download($filename, 'models.csv', $headers);
    }
}
