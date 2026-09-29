<?php

namespace Modules\Product\Repositories;

use Modules\Product\Entities\Brand;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\BrandExport;
use Modules\Product\Imports\BrandImport;
use Response;

class BrandRepository implements BrandRepositoryInterface
{
    public function all()
    {
        return Brand::with('products', 'products.skus')->latest()->get();
    }

    public function forSelectIdName()
    {
        return Brand::orderBy('name','asc')->select('name','id')->get();
    }

    public function serachBased($search_keyword)
    {
        return Brand::whereLike(['name', 'description'], $search_keyword)->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/brand-list.xlsx"))) {
          unlink(public_path("uploads/csv/brand-list.xlsx"));
        }
        return Excel::store(new BrandExport($data), 'uploads/csv/brand-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column, $relational_data = [], $selected_data = ['*'])
    {
        $brands = Brand::query();
        $brands = $brands->with($relational_data);
        if ($quick_search != null) {
            $brands = $brands->whereLike(['name'],$quick_search);
        }
        if ($row_count == "all") {
            $total_number = Brand::count();

            if ($column != null) {
                return $brands->orderBy($column, $sort)->paginate($total_number,$selected_data);
            }else {
                return $brands->latest()->paginate($total_number,$selected_data);
            }
        }else {
            if ($column != null) {
                return $brands->orderBy($column, $sort)->paginate($row_count,$selected_data);
            }else {
                return $brands->latest()->paginate($row_count,$selected_data);
            }
        }
    }

    public function create(array $data)
    {
        $variant = new Brand();
        $variant->fill($data)->save();
        return $variant;
    }

    public function find($id)
    {
        return Brand::findOrFail($id);
    }

    public function findforReport($id)
    {
        return Brand::where('id', $id)->get();
    }

    public function update(array $data, $id)
    {

        $variant = Brand::findOrFail($id);
        $variant->update($data);
    }

    public function delete($id)
    {
        return Brand::findOrFail($id)->delete();
    }

    public function csv_upload_brand($data)
    {
        if (!empty($data['file'])) {
            $fileName = time().'_'.$data['file']->getClientOriginalName();
            request()->file('file')->storeAs('reports', $fileName, 'public');

            Excel::import(new BrandImport, request()->file('file'));
        }
    }

    public function csv_download()
    {
        $table = Brand::all();
        $filename = "brands_tbl.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('id', 'name'));

        foreach($table as $row) {
            fputcsv($handle, array($row['id'], $row['name']));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::download($filename, 'brands_tbl.csv', $headers);
    }

    public function listForSelect($request)
    {
        if ($request->search != '') {
            $items = Brand::whereLike(['name'], $request->search)->where('status', 1)->paginate(10, ['id', 'name']);
        } else {
            $items = Brand::where('status', 1)->paginate(10, ['id', 'name']);
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
}
