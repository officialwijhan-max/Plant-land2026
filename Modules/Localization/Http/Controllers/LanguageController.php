<?php

namespace Modules\Localization\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Modules\Localization\Entities\Language;
use Modules\Localization\Repositories\LanguageRepositoryInterface;
use Artisan;
use Modules\Setting\Model\GeneralSetting;

class LanguageController extends Controller
{
    protected $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->middleware(['auth']);

        $this->languageRepository = $languageRepository;
    }

    public function index(Request $request)
    {

        try{
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;

            $data['items'] = $this->languageRepository->withPaginate($row_count,$quick_search,$sort,$column);
            if ($request->ajax()) {
                return view('localization::languages.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->languageRepository->withPaginate("all",$quick_search,$sort,$column);
                if ($request->import_as == "print") {
                    return view('localization::languages.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->languageRepository->csvDownload();
                    $filePath = public_path("uploads/csv/languages.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-languages.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('localization::languages.index', $data);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return back();
        }
    }

    public function update_rtl_status(Request $request)
    {
        try{
            $language = Language::findOrFail($request->id);
            $language->rtl = $request->status;
            if($language->save()){
                Artisan::call('cache:clear');
                return 1;
            }
            return 0;
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return back();
        }
    }


    public function update_active_status(Request $request)
    {

        $language = Language::findOrFail($request->id);
        if(($language->code == 'en' || $request->id == 19) && env('APP_SYNC')){
            return response()->json(['success' => false, 'error' => 'English language status update is disabled on demo mode.' ]);
        }
        $language->status = $request->status;
        if($language->save()){
            Cache::forget('languages');
            $languages = Language::where('status' ,1)->get();
            $output = '';
            if(session()->has('locale')){
                $locale = session('locale', app('general_setting')->language->code);
            }
            else{
                $locale = 'en';
            }


            foreach ($languages as $language)
            {


                $output .= '<option value="'.$language->code.'" '.($locale ==  $language->code ? 'selected' : '').'>'.$language->name.'</option>';
            }
            return response()->json([
                'success' => trans('Updated Successfully'),
                'languages' => $output,
            ]);
        }
        return response()->json(['error' => trans('common.Something Went Wrong')]);


    }


    public function store(Request $request)
    {
        $validate_rules = [
            'name' => 'required', 'native' => 'required', 'code' => 'required'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        try {
            $this->languageRepository->create($request->except("_token"));
            \LogActivity::successLog('Language Added Successfully');
            Toastr::success(__('setting.Language Added Successfully'));
            Artisan::call('cache:clear');
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function edit(Request $request)
    {
        try {
            $language = $this->languageRepository->find($request->id);
            return view('localization::languages.edit_modal', [
                "language" => $language
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function show($id)
    {
        try {
            $language = $this->languageRepository->find($id);
            if(($language->code == 'en' || $id == 19) && env('APP_SYNC')){
                Toastr::error('English language translation is disabled on demo mode.' );
                return redirect()->back();
            }
            session()->put('locale', $language->code);
            $lang = 'default';
            $files   = glob(resource_path('lang/' . $lang . '/*.php'));

            $modules = \Module::all();
            foreach ($modules as $module) {
                if ($module->isEnabled()) {
                    $file = glob(module_path($module->getName()) . '/Resources/lang/'.$lang.'/*.php');
                    if ($file) {
                        $files[$module->getLowerName()] = $file;
                    }
                }
            }

            return view('localization::languages.translate_view', [
                "files" => $files, 'language' => $language
            ]);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return $e->getMessage();
        }
    }

    public function update(Request $request, $id)
    {
        if($request->id == 19 && env('APP_SYNC')){
            throw ValidationException::withMessages(['message' => 'English language update is disabled on demo mode.']);
        }
        $validate_rules = [
            'name' => 'required', 'native' => 'required', 'code' => 'required'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $language = $this->languageRepository->update($request->except("_token"), $id);
            \LogActivity::successLog('Language Updated Successfully');
            Toastr::success(__('setting.Language Updated Successfully'));
            Artisan::call('cache:clear');
            return back();
        } catch (\Exception $e) {
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function key_value_store(Request $request)
    {
        $validate_rules = [
            'id' => 'required',
            'translatable_file_name' => 'required',
            'key' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try{
            $language = Language::findOrFail($request->id);

            $file_name = $request->translatable_file_name;

            $check_module = explode('::', $file_name);

            if (count($check_module) > 1) {
                $translatable_file_name = $check_module[1].'.php';
                $folder = module_path(ucfirst($check_module[0])).'/Resources/lang/'.$language->code.'/';
            } else{
                $translatable_file_name = $request->translatable_file_name.'.php';
                $folder = resource_path('lang/' . $language->code.'/');
            }

            $file = $folder . $translatable_file_name;

            if (!file_exists($folder)) {
                mkdir($folder);
            }
            if (file_exists($file)) {
                file_put_contents($file, '');
            }

            file_put_contents($file, '<?php return ' . var_export($request->key, true) . ';');

            \LogActivity::successLog($language->name. '- translated.');
            Artisan::call('cache:clear');
            Toastr::success('Operation Successfully done');
            return back();

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());

            Toastr::error('Operation failed');
            return back();
        }
    }

    public function changeLanguage(Request $request)
    {
        if(env('APP_SYNC')){
            return response()->json(['success' => false, 'error' => 'Change language feature is disabled on demo mode.' ]);
        }

        try {
            $language = $this->languageRepository->findByCode($request->code);

            $general_settings = GeneralSetting::first();
            $general_settings->language_id = $language->id;
            $general_settings->language_name = $request->code;
            $general_settings->save();
            session()->put('locale', $request->code);
            Artisan::call('cache:clear');
            return response()->json([
                'success' => trans('common.Successfully Updated')
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'error' => trans('common.Something Went Wrong')
            ]);
        }
    }

    public function get_translate_file(Request $request)
    {
        try{
            $language = $this->languageRepository->find($request->id);

            $file_name = $request->file_name;

            $languages = Lang::get($file_name);

            $check_module = explode('::', $file_name);

            if (count($check_module) > 1) {
                $translatable_file_name = $check_module[1].'.php';
                $folder = module_path(ucfirst($check_module[0])).'/Resources/lang/'.$language->code.'/';
                $default_folder = module_path(ucfirst($check_module[0])).'/Resources/lang/default/';
            } else{
                $translatable_file_name = $request->file_name.'.php';
                $folder = resource_path('lang/' . $language->code.'/');
                $default_folder = resource_path('lang/default/');
            }


            $file = $folder . $translatable_file_name;
            $default_file = $default_folder .$translatable_file_name;

            if(file_exists($file))
            {
                $languages = include  "{$file}";

                return view('localization::modals.translate_modal', [
                    "languages" => $languages,
                    "language" => $language,
                    "translatable_file_name" => $file_name
                ]);
            }


            if (!file_exists($folder)) {
                mkdir($folder);
            }

            if (!file_exists($file)) {
                copy($default_file, $file);
            }

            return view('localization::modals.translate_modal', [
                "languages" => $languages,
                "language" => $language,
                "translatable_file_name" => $file_name
            ]);
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return back();
        }

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $language = $this->languageRepository->delete($id);
            \LogActivity::successLog('Language has been deleted Successfully');
            Toastr::success(__('setting.Language has been deleted Successfully'));
            Artisan::call('cache:clear');
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back()->with('message-danger', __('common.Something Went Wrong'));
        }
    }
}
