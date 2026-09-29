<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_column_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('table_name')->nullable();
            $table->string('total_colum')->nullable();
            $table->string('show_column_no_by_admin')->nullable();
            $table->string('hide_column_no_by_admin')->nullable();
            $table->text('show_column_no_by_self')->nullable();
            $table->text('hide_column_no_by_self')->nullable();
            $table->text('export_column')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_column_permissions');
    }
};
