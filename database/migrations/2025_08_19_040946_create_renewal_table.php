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
      Schema::create('tbl_renewal', function (Blueprint $table) {
    $table->id('renewal_id');
    $table->unsignedBigInteger('scholar_id');
    $table->text('renewal_cert_of_reg');
    $table->text('renewal_grade_slip');
    $table->text('renewal_brgy_indigency');
    $table->string('renewal_semester', 20);
    $table->string('renewal_acad_year', 20);
    $table->date('renewal_start_date')->nullable();
    $table->date('renewal_deadline')->nullable();
    $table->date('date_submitted');
    $table->string('renewal_status', 50)->default('Pending');
    $table->text('rejection_reason')->nullable();
    
    $table->timestamps();

    $table->foreign('scholar_id')->references('scholar_id')->on('tbl_scholar')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_renewal');
    }
};
