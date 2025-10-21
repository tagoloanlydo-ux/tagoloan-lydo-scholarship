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
        Schema::create('tbl_settings', function (Blueprint $table) {
            $table->id();
            $table->date('application_start_date')->nullable();
            $table->date('application_deadline')->nullable();
            $table->date('renewal_start_date')->nullable();
            $table->date('renewal_deadline')->nullable();
            $table->string('renewal_semester', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_settings', function (Blueprint $table) {
            $table->dropColumn(['application_start_date', 'application_deadline', 'renewal_start_date', 'renewal_deadline']);
        });
    }
};
