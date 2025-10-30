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
        Schema::table('family_intake_sheets', function (Blueprint $table) {
            $table->dropColumn(['applicant_fname', 'applicant_mname', 'applicant_lname', 'applicant_suffix', 'applicant_gender']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_intake_sheets', function (Blueprint $table) {
            $table->string('applicant_fname', 255)->nullable();
            $table->string('applicant_mname', 255)->nullable();
            $table->string('applicant_lname', 255)->nullable();
            $table->string('applicant_suffix', 255)->nullable();
            $table->string('applicant_gender', 255)->nullable();
        });
    }
};
