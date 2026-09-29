<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\UserActivityLog\Traits\LogActivity;
use Modules\Setting\Repositories\PdfFontRepository;

class PdfFontsController extends Controller
{
    protected $pdfFontRepository;

    public function __construct(PdfFontRepository $pdfFontRepository)
    {
        $this->pdfFontRepository = $pdfFontRepository;
    }

    public function index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'desc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['items'] = $this->pdfFontRepository->withPaginate($row_count,$quick_search,$sort,$column);
            if ($request->ajax()) {
                return view('setting::pdfFont.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->pdfFontRepository->withPaginate("all",$quick_search,$sort,$column);
                if ($request->import_as == "print") {
                    return view('setting::pdfFont.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->pdfFontRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/pdf-fonts.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-pdf-fonts.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }

            return view('setting::pdfFont.index', $data);
        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|unique:pdf_fonts,name",
            "font_file" => "required",
        ]);

        try {
            $this->pdfFontRepository->create($request->except("_token"));

            return response()->json([
                'success' => trans('pdf.PDF Pont Added Successfully'),
            ]);
        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            "name" => "required|unique:pdf_fonts,name," . $request->id,
            "font_file" => "required",
        ]);

        try {
            $this->pdfFontRepository->update($request->all());
            LogActivity::successLog(trans('pdf.Font Updated Successfully'));

            return response()->json([
                'success' => trans('pdf.Font Updated Successfully'),
            ]);

        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }


    public function updateFontStatus(Request $request)
    {
        try {
            $this->pdfFontRepository->statusUpdate($request->except("_token"));
            LogActivity::successLog('Active Status has been updated.');
            return response()->json([
                'success' => trans('leave.Status has been updated Successfully'),
            ]);
        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());

            return response()->json([
                'error' => trans('common.Something Went Wrong')
            ]);
        }
    }

    private function loadTableData()
    {

        $data = [
            'fonts' => $this->pdfFontRepository->index()
        ];
        return (string)view('setting::pdfFont.components.list', $data);
    }


    public function destroy(Request $request)
    {
        try {
            $this->pdfFontRepository->delete($request->id);

            return response()->json([
                'success' => trans('pdf.PDF Font has been deleted Successfully'),
            ]);
        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }
}
