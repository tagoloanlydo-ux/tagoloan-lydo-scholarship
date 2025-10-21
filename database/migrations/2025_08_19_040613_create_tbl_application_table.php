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
        Schema::create('tbl_application', function (Blueprint $table) {
    $table->id('application_id');
    $table->unsignedBigInteger('applicant_id');
    $table->text('application_letter');
    $table->text('cert_of_reg');
    $table->text('grade_slip');
    $table->text('brgy_indigency');
    $table->text('student_id');
    $table->date('date_submitted');
    $table->timestamps();

    $table->foreign('applicant_id')->references('applicant_id')->on('tbl_applicant')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_application');
    }
};
