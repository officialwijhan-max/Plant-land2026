<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Contact\Entities\ContactModel;
use Modules\ProAccount\Entities\SubLeadger;
use App\User;
use App\Staff;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pro_sub_leadgers', function (Blueprint $table) {
            $table->id();
            $table->string('morphable_type')->nullable();
            $table->unsignedBigInteger('morphable_id')->nullable();
            $table->unsignedBigInteger("leadger_id")->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->boolean("is_active")->default(1);
            $table->boolean("is_blocked")->default(0);
            $table->double("current_balance", 28,2)->default(0);
            $table->text("description")->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->index(['is_active']);
            $table->timestamps();
        });

        $contacts = ContactModel::get(['id','contact_type','contact_id','name']);
        foreach($contacts as $item)
        {
            $sub_leadger = Subleadger::create([
                'leadger_id' => (strtolower($item->contact_type) == "supplier") ? 19 : 8,
                'code' => $item->contact_id,
                'name' => $item->name,
                'morphable_type' => get_class($item),
                'morphable_id' => $item->id,
                'description' => $item->contact_type.' Account Created'
            ]);
        }

        $staffs = Staff::with(['user:id,name'])->get(['id','user_id','employee_id']);
        foreach ($staffs as $key => $staff) {
            $sub_leadger = Subleadger::create([
                'leadger_id' => 33,
                'code' => 'Emp-' . sprintf("%06d", $staff->id),
                'name' => $staff->user->name . ' ' . $staff->employee_id,
                'morphable_type' => get_class($staff),
                'morphable_id' => $staff->id,
                'description' => 'Employee Account Created when added Employee'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_sub_leadgers');
    }
};
