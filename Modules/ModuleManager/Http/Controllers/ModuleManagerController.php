<?php

namespace Modules\ModuleManager\Http\Controllers;

use App\Traits\UploadTheme;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\ModuleManager\Entities\InfixModuleManager;
use Modules\ModuleManager\Entities\Module;
use ZipArchive;

class ModuleManagerController extends Controller
{
    use UploadTheme;

    public function ModuleRefresh()
    {
        try {
//            exec('php composer.phar dump-autoload');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Toastr::success('Refresh successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Your server doesn\'t allow this refresh.' . $e->getMessage(), 'Failed');
            return redirect('');
        }
    }

    public function ManageAddOns()
    {
        try {
            $module_list = [];
            $is_module_available = Module::all();
            return view('modulemanager::manage_module', compact('is_module_available', 'module_list'));
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), trans('common.Failed'));
            return redirect('');
        }
    }

    public function moduleAddOnsEnable($name)
    {
        if (config('app.app_sync')) {
            return response()->json(['error' => 'Restricted in demo mode']);
        }

        try {

            $module_tables = [];
            $module_tables_names = [];
            $dataPath = 'Modules/' . $name . '/' . $name . '.json';        // // Get the contents of the JSON file
            $strJsonFileContents = file_get_contents($dataPath);
            $array = json_decode($strJsonFileContents, true);
            $migrations = $array[$name]['migration'] ?? '';
            $names = $array[$name]['names'];


            $version = $array[$name]['versions'][0] ?? '';
            $url = $array[$name]['url'][0] ?? '';
            $notes = $array[$name]['notes'][0] ?? '';


            DB::beginTransaction();
            $s = InfixModuleManager::where('name', $name)->first();
            if (empty($s)) {
                $s = new InfixModuleManager();
            }
            $s->name = $name;
            $s->notes = $notes;
            $s->version = $version;
            $s->update_url = $url;
            $s->installed_domain = url('/');
            $s->activated_date = date('Y-m-d');
            $s->save();
            DB::commit();


            if (!empty($migrations) && count($migrations) != 0)
                foreach ($migrations as $value) {
                    $module_tables[] = 'Modules/' . $name . '/Database/Migrations/' . $value;
                }

            foreach ($names as $value) {
                $module_tables_names[] = $value;
            }

            $is_module_available = 'Modules/' . $name . '/Providers/' . $name . 'ServiceProvider.php';

            if (file_exists($is_module_available)) {
                try {

                    $ModuleManage = Module::where('name', $name)->first();


                    if (!moduleStatusCheck($name)) {
                        $ModuleManage->status = 1;
                        $ModuleManage->save();

                        $moduleCheck = \Nwidart\Modules\Facades\Module::find($name);
                        $moduleCheck->enable();

                        if(!$this->moduleMigration($name)){
                            $moduleCheck->disable();
                        }else{
                            $data['data'] = 'enable';
                            $data['success'] = __('common.Successfully Updated');
                        }

                        return response()->json($data, 200);
                    } else {

                        $ModuleManage = Module::where('name', $name)->first();
                        $ModuleManage->status = 0;
                        $ModuleManage->save();

                        $moduleCheck = \Nwidart\Modules\Facades\Module::find($name);
                        $moduleCheck->disable();

                        $data['data'] = 'disable';
                        $data['Module'] = $ModuleManage;
                    }


                    $data['success'] = __('common.Successfully Updated');

                    return response()->json($data, 200);
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                    $data['error'] = $e->getMessage();
                    return response()->json($data, 200);
                }
            } else {
                $data['error'] = __('common.Operation failed');
                return response()->json($data, 200);
            }


        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $ModuleManage = Module::where('name', $name)->first();
            $ModuleManage->status = 0;
            $ModuleManage->save();
            $moduleCheck = \Nwidart\Modules\Facades\Module::find($name);
            if ($moduleCheck) {
                $moduleCheck->disable();
            }
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }

    }


    public function moduleAddOnsDisable($name)
    {
        if (config('app.app_sync')) {
            return response()->json(['error' => 'Restricted in demo mode']);
        }

        try {
            $ModuleManage = Module::where('name', $name)->first();
            $ModuleManage->status = 0;
            $ModuleManage->save();

            $moduleCheck = \Nwidart\Modules\Facades\Module::find($name);
            if ($moduleCheck) {
                $moduleCheck->disable();
            }

            $data['data'] = 'disable';
            $data['Module'] = $ModuleManage;
            $data['success'] = __('common.Successfully Updated');

            return response()->json($data, 200);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function FreemoduleAddOnsEnable($name)
    {


        try {

            $module_tables = [];
            $module_tables_names = [];
            $dataPath = 'Modules/' . $name . '/' . $name . '.json';        // // Get the contents of the JSON file
            $strJsonFileContents = file_get_contents($dataPath);
            $array = json_decode($strJsonFileContents, true);
            $migrations = $array[$name]['migration'] ?? '';
            $names = $array[$name]['names'];


            $version = $array[$name]['versions'][0] ?? '';
            $url = $array[$name]['url'][0] ?? '';
            $notes = $array[$name]['notes'][0] ?? '';


            DB::beginTransaction();
            $s = InfixModuleManager::where('name', $name)->first();
            if (empty($s)) {
                $s = new InfixModuleManager();
            }
            $s->name = $name;
            $s->notes = $notes;
            $s->version = $version;
            $s->update_url = $url;
            $s->installed_domain = url('/');
            $s->activated_date = date('Y-m-d');
            $s->save();
            DB::commit();


            if (!empty($migrations) && count($migrations) != 0)
                foreach ($migrations as $value) {
                    $module_tables[] = 'Modules/' . $name . '/Database/Migrations/' . $value;
                }

            foreach ($names as $value) {
                $module_tables_names[] = $value;
            }

            $is_module_available = 'Modules/' . $name . '/Providers/' . $name . 'ServiceProvider.php';

            if (file_exists($is_module_available)) {
                try {

                    if (!empty($module_tables)) {
                        foreach ($module_tables as $table) {
                            $path = $table;
                            if (file_exists($path)) {
                                try {
                                    Artisan::call('migrate',
                                        array(
                                            '--path' => $path,
                                            '--force' => true));


                                } catch (\Exception $e) {
                                    Log::info($e->getMessage());

                                }
                            }
                        }
                    }


                    $moduleCheck = \Nwidart\Modules\Facades\Module::find($name);
                    $moduleCheck->enable();


                    $ModuleManage = Module::where('name', $name)->first();


                } catch (\Exception $e) {
                    Log::info($e->getMessage());

                }
            } else {
                Log::info('module not found');
                DB::rollback();
            }


        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollback();
        }

    }

    public function moduleMigration($module)
    {
        try {
            Artisan::call('module:migrate', [
                'module' => $module,
                '--force' => true
            ]);

            return true;
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }

    }

}
