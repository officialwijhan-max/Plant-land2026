<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Project\Entities\Project;
use App\User;

/**
 * Landscape jobs represented as Projects in the built-in task-management
 * module (Workspace/Team id 1 is auto-seeded by their own migrations).
 * The real invoicing side of these jobs is PurchaseSeeder/SaleSeeder -
 * this is just the internal work-tracking view of the same jobs.
 */
class ProjectSeeder extends Seeder
{
    public function run()
    {
        if (Project::count() > 0) {
            $this->command->info('projects already has data, skipping.');
            return;
        }

        $adminId = DB::table('users')->where('role_id', 1)->value('id') ?? 1;
        $engineers = User::whereHas('role', fn ($q) => $q->where('name', 'Site Engineer'))->pluck('id');

        $projects = [
            ['فيلا خاصة - أ. محمد رشدي: تصميم وتنفيذ حديقة', 'Landscape design and installation for a private villa garden in New Cairo.', 45],
            ['فيلا خاصة - د. سلمى عبد العزيز: تنسيق حديقة', 'Full garden landscaping for a private villa in Sheikh Zayed.', 60],
            ['مستشفى الشفاء التخصصي: المساحات الخضراء', 'Green space design and planting for hospital grounds.', 90],
            ['كمبوند الياسمين: صيانة وتطوير الحدائق المشتركة', 'Ongoing maintenance and development of compound common-area gardens.', 120],
            ['فيلا خاصة - م. أحمد الجندي: شبكة ري وتشجير', 'Irrigation network and tree planting for a private villa.', 30],
            ['كمبوند واحة النخيل: المرحلة الأولى من الحدائق', 'Phase one landscaping for a residential compound.', 100],
            ['فندق النيل الكبير: تجديد حديقة الفندق', 'Hotel garden renovation project.', 75],
            ['مجمع إداري النور: تنسيق المساحات الخضراء', 'Green space landscaping for a business complex.', 50],
        ];

        $count = 0;
        foreach ($projects as [$name, $description, $daysOut]) {
            $project = Project::create([
                'name' => $name,
                'user_id' => $adminId,
                'team_id' => 1,
                'description' => $description,
                'privacy' => 1,
                'default_view' => 'list',
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'due_date' => now()->addDays($daysOut)->toDateString(),
            ]);

            $project->users()->attach($adminId);
            foreach ($engineers as $engineerId) {
                $project->users()->syncWithoutDetaching([$engineerId]);
            }

            $count++;
        }

        $this->command->info("Seeded {$count} landscape projects.");
    }
}
