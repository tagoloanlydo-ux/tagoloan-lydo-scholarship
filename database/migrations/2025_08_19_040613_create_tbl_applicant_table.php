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
       Schema::create('tbl_applicant', function (Blueprint $table) {
    $table->id('applicant_id');
    $table->string('applicant_fname', 50);
    $table->string('applicant_mname', 50)->nullable();
    $table->string('applicant_lname', 50);
    $table->string('applicant_suffix', 10)->nullable();
    $table->string('applicant_gender', 10);
    $table->date('applicant_bdate');
    $table->string('applicant_civil_status', 20);
    $table->string('applicant_brgy', 100);
    $table->string('applicant_email', 100)->unique();
    $table->string('applicant_contact_number', 20);
    $table->string('applicant_school_name', 100);
    $table->string('applicant_year_level', 20);
    $table->string('applicant_course', 100);
    $table->string('applicant_acad_year', 20);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_applicant');
    }
};
