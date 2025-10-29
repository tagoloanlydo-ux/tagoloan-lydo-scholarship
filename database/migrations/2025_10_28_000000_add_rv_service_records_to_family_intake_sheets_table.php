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
            $table->json('rv_service_records')->nullable()
                  ->comment('JSON array of RV service records (date, problem, assistance, remarks)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_intake_sheets', function (Blueprint $table) {
            $table->dropColumn('rv_service_records');
        });
    }
};
