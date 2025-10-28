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
        Schema::table('tbl_application_personnel', function (Blueprint $table) {
            $table->boolean('intake_sheet_submitted')->default(false)->after('update_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_application_personnel', function (Blueprint $table) {
            $table->dropColumn('intake_sheet_submitted');
        });
    }
};
