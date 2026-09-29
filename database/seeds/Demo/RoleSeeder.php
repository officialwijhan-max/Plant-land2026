<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\RolePermission\Entities\Permission;
use Modules\RolePermission\Entities\Role;

/**
 * Landscaping-company roles beyond the 5 the app ships with (Super admin,
 * Admin, Staff, Supplier, Customer - seeded by the roles migration).
 *
 * Settings access: module_id 5 is the entire Settings module tree. It is
 * deliberately never attached to any role here - Settings is locked to
 * Super Admin (role_id 1) by app/Http/Middleware/EnsureSuperAdmin.php at
 * the route level, independent of this permission table. CEO/GM are given
 * role type 'system_user', which makes the general Permission middleware
 * bypass its checks entirely (see app/Http/Middleware/Permission.php) -
 * that's how they get "everything else" without hand-listing every
 * permission - but EnsureSuperAdmin still blocks them from Settings
 * regardless of role type, since it checks role_id === 1 specifically.
 */
class RoleSeeder extends Seeder
{
    /**
     * module_id groupings per role (see permissions migrations for the
     * module_id => module name mapping). Deliberately excludes 5 (Settings)
     * everywhere. This is a reasonable, not perfectly exhaustive, split for
     * a landscaping business - some modules (e.g. 13 covers both Purchase
     * and Sale) can't be separated more finely without hand-listing
     * hundreds of individual permission IDs.
     */
    protected $roleModules = [
        'Sales' => [1, 9, 13, 14, 15, 22],
        'Accountant' => [1, 9, 18, 20],
        'Head of Accountants' => [1, 7, 9, 13, 18, 20],
        'HR' => [1, 10, 11],
        'Warehouse Keeper' => [1, 2, 13, 14],
        'Site Engineer' => [1, 9, 15, 22],
    ];

    public function run()
    {
        $adminId = DB::table('users')->where('role_id', 1)->value('id') ?? 1;

        $regularRoles = array_keys($this->roleModules);
        $systemRoles = ['GM', 'CEO'];

        foreach ($regularRoles as $name) {
            $role = Role::firstOrCreate(
                ['name' => $name],
                ['type' => 'regular_user', 'details' => $name . ' (landscaping)']
            );

            $permissionIds = Permission::whereIn('module_id', $this->roleModules[$name])
                ->where('module_id', '!=', 5)
                ->pluck('id');

            $role->permissions()->sync($permissionIds);

            $this->command->info("Role '{$name}': " . $permissionIds->count() . ' permissions attached.');
        }

        foreach ($systemRoles as $name) {
            Role::firstOrCreate(
                ['name' => $name],
                ['type' => 'system_user', 'details' => $name . ' - full access except Settings']
            );
            $this->command->info("Role '{$name}': system_user (bypasses permission checks; still blocked from Settings by EnsureSuperAdmin).");
        }
    }
}
