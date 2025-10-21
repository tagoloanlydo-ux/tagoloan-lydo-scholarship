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
       Schema::create('tbl_scholar', function (Blueprint $table) {
    $table->id('scholar_id');
    $table->unsignedBigInteger('application_id');
    $table->string('scholar_username', 50)->nullable();
    $table->string('scholar_pass')->nullable();
    $table->date('date_activated');
    $table->string('scholar_status', 50)->default('Active');
    $table->timestamps();

    $table->foreign('application_id')->references('application_id')->on('tbl_application')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_scholar');
    }
};
