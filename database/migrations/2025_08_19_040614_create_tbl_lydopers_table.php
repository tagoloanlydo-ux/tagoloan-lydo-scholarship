<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('tbl_lydopers', function (Blueprint $table) {
    $table->id('lydopers_id');
    $table->string('lydopers_fname', 50);
    $table->string('lydopers_mname', 50)->nullable();
    $table->string('lydopers_lname', 50);
    $table->string('lydopers_suffix', 10)->nullable();
    $table->string('lydopers_address')->nullable();
    $table->date('lydopers_bdate')->nullable();
    $table->string('lydopers_email', 100)->unique();
    $table->unsignedBigInteger('lydopers_contact_number');
    $table->string('lydopers_username', 50)->unique();
    $table->string('lydopers_pass');
    $table->string('lydopers_role', 50);
    $table->string('lydopers_status', 50);
    $table->timestamps();
});

    }
    public function down(): void
    {
        Schema::dropIfExists('tbl_lydopers');
    }
};
