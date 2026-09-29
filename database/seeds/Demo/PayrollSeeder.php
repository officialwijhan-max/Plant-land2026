<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Payroll\Entities\Payroll;
use Modules\Payroll\Entities\PayrollEarnDeduce;

/**
 * Two monthly payroll runs (this month and last month) per seeded
 * employee, based on their Staff.basic_salary.
 */
class PayrollSeeder extends Seeder
{
    public function run()
    {
        if (Payroll::count() > 0) {
            $this->command->info('payrolls already has data, skipping.');
            return;
        }

        $adminId = DB::table('users')->where('role_id', 1)->value('id') ?? 1;

        $staff = DB::table('staffs')->get();
        if ($staff->isEmpty()) {
            $this->command->warn('No staff found - run EmployeeSeeder first.');
            return;
        }

        $months = [now()->subMonthNoOverflow(), now()];
        $count = 0;

        foreach ($months as $monthDate) {
            foreach ($staff as $person) {
                $basic = (float) $person->basic_salary;
                $transportAllowance = round($basic * 0.10, 2);
                $performanceBonus = rand(0, 1) ? round($basic * 0.05, 2) : 0;
                $totalEarning = $transportAllowance + $performanceBonus;

                $socialInsurance = round($basic * 0.11, 2);
                $totalDeduction = $socialInsurance;

                $grossSalary = $basic + $totalEarning;
                $tax = round(max(0, $grossSalary - 15000) * 0.10, 2); // simple bracket, not real Egyptian tax law
                $netSalary = $grossSalary - $totalDeduction - $tax;

                $payroll = Payroll::create([
                    'staff_id' => $person->id,
                    'role_id' => DB::table('users')->where('id', $person->user_id)->value('role_id'),
                    'basic_salary' => $basic,
                    'total_earning' => $totalEarning,
                    'total_deduction' => $totalDeduction,
                    'gross_salary' => $grossSalary,
                    'tax' => $tax,
                    'net_salary' => $netSalary,
                    'payroll_month' => $monthDate->format('F'),
                    'payroll_year' => $monthDate->format('Y'),
                    'payroll_status' => 'P',
                    'payment_mode' => 'bank',
                    'payment_date' => $monthDate->copy()->endOfMonth()->toDateString(),
                    'active_status' => 1,
                    'created_by' => $adminId,
                ]);

                PayrollEarnDeduce::create([
                    'type_name' => 'Transport Allowance',
                    'amount' => $transportAllowance,
                    'earn_dedc_type' => 'e',
                    'payroll_id' => $payroll->id,
                    'created_by' => $adminId,
                ]);

                if ($performanceBonus > 0) {
                    PayrollEarnDeduce::create([
                        'type_name' => 'Performance Bonus',
                        'amount' => $performanceBonus,
                        'earn_dedc_type' => 'e',
                        'payroll_id' => $payroll->id,
                        'created_by' => $adminId,
                    ]);
                }

                PayrollEarnDeduce::create([
                    'type_name' => 'Social Insurance',
                    'amount' => $socialInsurance,
                    'earn_dedc_type' => 'd',
                    'payroll_id' => $payroll->id,
                    'created_by' => $adminId,
                ]);

                $count++;
            }
        }

        $this->command->info("Seeded {$count} payroll runs (2 months x " . $staff->count() . ' employees) with earnings/deductions.');
    }
}
