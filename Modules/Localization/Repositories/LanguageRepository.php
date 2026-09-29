<?php

namespace Modules\Localization\Repositories;

use Modules\Localization\Entities\Language;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Localization\Exports\LanguageExport;
use Modules\Localization\Repositories\LanguageRepositoryInterface;

class LanguageRepository implements LanguageRepositoryInterface
{
    public function all()
    {
        return Language::orderBy('status', 'desc')->get();
    }

    public function csvDownload()
    {
        if (file_exists(public_path("uploads/csv/languages.xlsx"))) {
          unlink(public_path("uploads/csv/languages.xlsx"));
        }
        return Excel::store(new LanguageExport, 'uploads/csv/languages.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column)
    {
        $items = Language::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['name','code'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Language::count();

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

    public function serachBased($search_keyword)
    {
        return Language::whereLike(['name', 'native', 'code'], $search_keyword)->get();
    }

    public function create(array $data)
    {
        $language = new Language();
        $language->fill($data)->save();
    }

    public function find($id)
    {
        return Language::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        return Language::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        return Language::findOrFail($id)->delete();
    }

    public function findByCode($code)
    {
        return Language::where('code', $code)->first();
    }
}
