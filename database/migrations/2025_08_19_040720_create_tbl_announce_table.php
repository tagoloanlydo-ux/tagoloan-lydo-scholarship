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
      Schema::create('tbl_announce', function (Blueprint $table) {
    $table->id('announce_id');
    $table->unsignedBigInteger('lydopers_id'); // MUST MATCH parent
    $table->string('announce_title');
    $table->text('announce_content');
    $table->string('announce_type');
    $table->date('date_posted');
    $table->timestamps();

    $table->foreign('lydopers_id')
        ->references('lydopers_id')
        ->on('tbl_lydopers')
        ->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_announce');
    }
};
