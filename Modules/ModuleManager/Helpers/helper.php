<?php

if (!function_exists('moduleStatusCheck')) {
    function moduleStatusCheck($module): bool
    {

        try {
            $haveModule = \Modules\ModuleManager\Entities\Module::where('name', $module)->first();
            if (empty($haveModule)) {
                return false;
            }
            $moduleStatus = $haveModule->status;

            $is_module_available = 'Modules/' . $module . '/Providers/' . $module . 'ServiceProvider.php';

            if (file_exists($is_module_available)) {

                $moduleCheck = \Nwidart\Modules\Facades\Module::find($module)->isEnabled();
                if (!$moduleCheck) {
                    return false;
                }

                // No purchase-code/license verification required — a module
                // is active as long as it's installed, enabled, and marked
                // active in the module manager.
                if ($moduleStatus == 1) {
                    return true;
                }
            }


            return false;
        } catch (\Throwable $th) {

            return false;
        }

    }
}
