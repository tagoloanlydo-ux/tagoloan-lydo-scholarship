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
            $table->text('reviewer_comment')->nullable()->after('rejection_reason');
            $table->boolean('is_bad')->default(false)->after('reviewer_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_application_personnel', function (Blueprint $table) {
            $table->dropColumn(['reviewer_comment', 'is_bad']);
        });
    }
};
