nk<?php

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
        Schema::create('tbl_disbursement', function (Blueprint $table) {
            $table->id('disburse_id');
            $table->unsignedBigInteger('scholar_id');
            $table->decimal('disburse_amount', 10, 2);
            $table->string('disburse_semester');
            $table->string('disburse_acad_year');
            $table->text('disburse_signature')->nullable();
            $table->timestamps();

            $table->foreign('scholar_id')->references('scholar_id')->on('tbl_scholar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_disbursement');
    }
};
