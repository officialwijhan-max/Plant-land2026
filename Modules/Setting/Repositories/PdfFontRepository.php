<?php

namespace Modules\Setting\Repositories;

use App\Traits\ImageStore;
use Illuminate\Support\Str;
use Modules\Setting\Entities\PdfFont;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setting\Exports\PdfFontExport;

class PdfFontRepository
{
    use ImageStore;

    public function index()
    {
        return PdfFont::latest()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/pdf-fonts.xlsx"))) {
          unlink(public_path("uploads/csv/pdf-fonts.xlsx"));
        }
        return Excel::store(new PdfFontExport($data), 'uploads/csv/pdf-fonts.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column, $relational_data = [], $selected_data = ['*'])
    {
        $brands = PdfFont::query();
        if ($quick_search != null) {
            $brands = $brands->whereLike(['name'],$quick_search);
        }
        if ($row_count == "all") {
            $total_number = PdfFont::count();

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
        $files = $this->fileSave($data);

        $data['font_file'] = $files['name'];
        $data['name'] = strtolower(Str::camel($data['name']));

        return PdfFont::create($data);
    }


    public function fileSave($data): array
    {
        $name ='';

        if (array_key_exists('font_file', $data) && $data['font_file']) {
            $font_file = $data['font_file'];
            $name = $font_file->getClientOriginalName();
            $font_file->move('public/fonts/', $name);
        }
        return [
            'name' => $name
        ];
    }

    public function statusUpdate($data)
    {
        if ($data['status'] == 1) {
            PdfFont::where('is_active', 1)->update(['is_active' => 0]);
        }
        $newFont = PdfFont::find($data['id']);
        $newFont->is_active = $data['status'];
        $newFont->save();
    }

    public function delete($id)
    {
        $pdfFont = PdfFont::findOrFail($id);
        $file_path = 'public/fonts/' . $pdfFont->font_file;
        $this->deleteImage($file_path);
        return $pdfFont->delete();

    }
}
