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
            // Add the missing columns for intake sheet functionality
            $table->string('intake_sheet_token', 64)->nullable()->after('status');
            $table->boolean('intake_sheet_submitted')->default(false)->after('intake_sheet_token');
            $table->string('update_token', 64)->nullable()->after('intake_sheet_submitted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_application_personnel', function (Blueprint $table) {
            $table->dropColumn(['intake_sheet_token', 'intake_sheet_submitted', 'update_token']);
        });
    }
};