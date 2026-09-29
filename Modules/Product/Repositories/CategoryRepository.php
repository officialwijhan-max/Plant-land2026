<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Arr;
use Modules\Product\Entities\Category;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\CategoryExport;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::with("categories",'parentCat')
            ->orderBy("id", "DESC")
            ->get();
    }

    public function serachBased($search_keyword)
    {
        return Category::whereLike(['name', 'code', 'parent_id'], $search_keyword)->get();
    }

    public function csvDownload($data)
    {

        if (file_exists(public_path("uploads/csv/category.xlsx"))) {
            unlink(public_path("uploads/csv/category.xlsx"));
        }
        return Excel::store(new CategoryExport($data), 'uploads/csv/category.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = Category::query();
        $items = $items->with($relational_data)->where('parent_id', null);
        if ($quick_search != null && $quick_search != '') {
            $items = $items->whereLike(['name', 'code'], $quick_search);
        }

        if ($row_count == "all") {
            $total_number = Category::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function create(array $data)
    {
        $category = new Category();
        if (isset($data['as_sub_category']) && $data['as_sub_category'] == 1) {
            $parent_account = $this->find($data['parent_id']);
            $data = Arr::add($data, "level", $parent_account ? ($parent_account->level + 1) : 1 );
            $data = Arr::add($data, "parent_id", $data['parent_id']);
        } else {
            $data = Arr::add($data, "level",0);
            $data = Arr::set($data, "parent_id", null);
        }
        $category->fill($data)->save();
        return $category;
    }

    public function find($id)
    {
        return Category::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $variant = Category::findOrFail($id);
        if (isset($data['as_sub_category']) && $data['as_sub_category'] == 1) {
            $data = Arr::add($data, "parent_id", $data['parent_id']);
        } else {
            $data = Arr::set($data, "parent_id", null);
        }
        $variant->update($data);
    }

    public function category()
    {
        return Category::where("parent_id", null)->get();
    }

    public function subcategory($category)
    {
        return Category::where("parent_id", $category)->get();
    }

    public function allSubCategory()
    {
        return Category::where("parent_id", "!=", null)->get();
    }

    public function delete($id)
    {
        return Category::findOrFail($id)->delete();
    }

    public function listForSelect($search)
    {
        if ($search != '')
            $items = Category::whereLike(['name'], $search)->where('status', 1)->paginate(10, ['id', 'name']);
        else
            $items = Category::where('status', 1)->paginate(10, ['id', 'name']);

        $response = [];
        foreach ($items as $item) {
            $response[]  = [
                'id'    => $item->id,
                'text'  => $item->code ? $item->name . ' (' . $item->code . ')' : $item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function listForSelectByCategory($category_id, $search)
    {
        $sub_categories = Category::query();
        $sub_categories->where('status', 1);

        if ($category_id != '' && $category_id != 0) {
            $sub_categories->where("parent_id", $category_id);
        } else {
            $sub_categories->where("parent_id", "!=", null);
        }
        if ($search != '')
            $items = $sub_categories->whereLike(['name'], $search)->paginate(10, ['id', 'name']);
        else
            $items = $sub_categories->paginate(10, ['id', 'name']);

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
