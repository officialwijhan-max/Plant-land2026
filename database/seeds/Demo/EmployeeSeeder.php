<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Staff;
use App\User;
use Modules\RolePermission\Entities\Role;
use Modules\Setup\Entities\Department;

/**
 * 2-3 employees per role for the landscaping company (GM/CEO get one each
 * - they're singular leadership positions, not pooled roles). All demo
 * accounts share the password below - change or remove these before real
 * use, same as with any other test data.
 */
class EmployeeSeeder extends Seeder
{
    const DEMO_PASSWORD = 'Employee@123';

    public function run()
    {
        $departments = [
            'Sales' => Department::firstOrCreate(['name' => 'Sales'], ['details' => 'Sales Department', 'status' => true]),
            'Accounting' => Department::firstOrCreate(['name' => 'Accounting'], ['details' => 'Accounting Department', 'status' => true]),
            'HR' => Department::firstOrCreate(['name' => 'HR'], ['details' => 'HR Department', 'status' => true]),
            'Operations' => Department::firstOrCreate(['name' => 'Operations'], ['details' => 'Warehouse & Operations', 'status' => true]),
            'Engineering' => Department::firstOrCreate(['name' => 'Engineering'], ['details' => 'Site Engineering', 'status' => true]),
            'Management' => Department::firstOrCreate(['name' => 'Management'], ['details' => 'Executive Management', 'status' => true]),
        ];

        // [role name, department key, basic_salary range, [employee names]]
        $plan = [
            ['Sales', 'Sales', [8000, 12000], ['Ahmed Mostafa Ali', 'Mariam Khaled Ibrahim', 'Youssef Hassan Fathy']],
            ['Accountant', 'Accounting', [10000, 14000], ['Sara Ahmed Mahmoud', 'Omar Adel Sayed']],
            ['Head of Accountants', 'Accounting', [18000, 22000], ['Khaled Mohamed Fouad']],
            ['HR', 'HR', [9000, 12000], ['Nourhan Tarek Abdelrahman', 'Amr Samir Aziz']],
            ['Warehouse Keeper', 'Operations', [6000, 8000], ['Mohamed Saeed Gomaa', 'Islam Farouk Ramadan', 'Hany Naguib Salem']],
            ['Site Engineer', 'Engineering', [12000, 16000], ['Karim Nabil Shawky', 'Dina Reda Fahmy', 'Tamer Osama Zaki']],
            ['GM', 'Management', [35000, 45000], ['Sherif Adel Mansour']],
            ['CEO', 'Management', [50000, 70000], ['Waleed Ashraf Nour']],
        ];

        $count = 0;
        foreach ($plan as [$roleName, $deptKey, $salaryRange, $names]) {
            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                $this->command->warn("Role '{$roleName}' not found - run RoleSeeder first. Skipping.");
                continue;
            }

            foreach ($names as $i => $name) {
                $slug = strtolower(str_replace(' ', '.', $name));
                $email = $slug . '@plantland-eg.com';

                if (User::where('email', $email)->exists()) {
                    continue;
                }

                $user = User::create([
                    'name' => $name,
                    'username' => $slug,
                    'email' => $email,
                    'role_id' => $role->id,
                    'email_verified_at' => now(),
                    'password' => Hash::make(self::DEMO_PASSWORD),
                ]);

                $joinMonthsAgo = rand(3, 30);

                Staff::create([
                    'user_id' => $user->id,
                    'department_id' => $departments[$deptKey]->id,
                    'showroom_id' => 1,
                    'warehouse_id' => 1,
                    'phone' => '01' . rand(0, 2) . rand(10000000, 99999999),
                    'basic_salary' => rand($salaryRange[0], $salaryRange[1]),
                    'employment_type' => 'Full Time',
                    'date_of_joining' => now()->subMonths($joinMonthsAgo)->toDateString(),
                ]);

                $count++;
            }
        }

        $this->command->info("Seeded {$count} employees. All demo accounts use the password: " . self::DEMO_PASSWORD);
    }
}
