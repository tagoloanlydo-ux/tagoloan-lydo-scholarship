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
        Schema::create('tbl_disburse', function (Blueprint $table) {
    $table->id('disburse_id');
    $table->unsignedBigInteger('scholar_id');
    $table->unsignedBigInteger('lydopers_id');
    $table->string('disburse_semester', 20);
    $table->string('disburse_acad_year', 20);
    $table->decimal('disburse_amount', 10, 2);
    $table->text('disburse_signature')->nullable();
    $table->date('disburse_date');
    $table->timestamps();

    $table->foreign('scholar_id')->references('scholar_id')->on('tbl_scholar')->onDelete('cascade');
    $table->foreign('lydopers_id')->references('lydopers_id')->on('tbl_lydopers')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_disburse');
    }
};
