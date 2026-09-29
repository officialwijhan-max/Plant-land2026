<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ~2 months of daily attendance for every seeded employee, respecting
 * Egypt's Friday/Saturday weekend. Valid codes per
 * Modules/Attendance/Resources/views/attendances/create_attendance.blade.php:
 * P (present), L (late), A (absent).
 */
class AttendanceSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('attendances')->count() > 0) {
            $this->command->info('attendances already has data, skipping.');
            return;
        }

        $adminId = DB::table('users')->where('role_id', 1)->value('id') ?? 1;

        $staff = DB::table('staffs')
            ->join('users', 'staffs.user_id', '=', 'users.id')
            ->select('staffs.user_id', 'users.role_id')
            ->get();

        if ($staff->isEmpty()) {
            $this->command->warn('No staff found - run EmployeeSeeder first.');
            return;
        }

        $rows = [];
        $start = now()->subDays(60);
        $end = now();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            // Egypt weekend: Friday (5) and Saturday (6).
            if (in_array($date->dayOfWeek, [5, 6])) {
                continue;
            }

            foreach ($staff as $person) {
                $roll = rand(1, 100);
                $status = $roll <= 5 ? 'A' : ($roll <= 12 ? 'L' : 'P');

                $rows[] = [
                    'attendance' => $status,
                    'date' => $date->toDateString(),
                    'day' => $date->format('l'),
                    'month' => $date->format('F'),
                    'year' => (int) $date->format('Y'),
                    'note' => $status === 'A' ? 'Absent' : ($status === 'L' ? 'Arrived late' : null),
                    'user_id' => $person->user_id,
                    'role_id' => $person->role_id,
                    'created_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $count = 0;
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('attendances')->insert($chunk);
            $count += count($chunk);
        }

        $this->command->info("Seeded {$count} attendance records for " . $staff->count() . ' employees over ~2 months.');
    }
}
