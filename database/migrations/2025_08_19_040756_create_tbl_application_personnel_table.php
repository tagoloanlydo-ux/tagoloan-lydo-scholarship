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
        Schema::create('tbl_application_personnel', function (Blueprint $table) {
    $table->id('application_personnel_id');
    $table->unsignedBigInteger('application_id');
    $table->unsignedBigInteger('lydopers_id');
    $table->string('document_type')->nullable(); // application_letter, cert_of_reg, grade_slip, etc.
    $table->text('reason')->nullable(); // Changed from comment to reason
    $table->boolean('is_bad')->default(0);
    $table->string('application_letter_status')->nullable();
    $table->string('cert_of_reg_status')->nullable();
    $table->string('grade_slip_status')->nullable();
    $table->string('brgy_indigency_status')->nullable();
    $table->string('student_id_status')->nullable();
    $table->string('initial_screening', 50)->default('Pending');
    $table->text('rejection_reason')->nullable();
    $table->text('remarks')->default('Pending');
    $table->string('status', 50)->default('Waiting');
    $table->timestamps();

    $table->foreign('application_id')->references('application_id')->on('tbl_application')->onDelete('cascade');
    $table->foreign('lydopers_id')->references('lydopers_id')->on('tbl_lydopers')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_application_personnel');
    }
};
