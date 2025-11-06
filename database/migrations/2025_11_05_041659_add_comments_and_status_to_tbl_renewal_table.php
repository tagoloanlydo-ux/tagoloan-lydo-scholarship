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
        Schema::table('tbl_renewal', function (Blueprint $table) {
            // Add status columns if they don’t exist
            if (!Schema::hasColumn('tbl_renewal', 'cert_of_reg_status')) {
                $table->enum('cert_of_reg_status', ['good', 'bad'])->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('tbl_renewal', 'grade_slip_status')) {
                $table->enum('grade_slip_status', ['good', 'bad'])->nullable()->after('cert_of_reg_status');
            }
            if (!Schema::hasColumn('tbl_renewal', 'brgy_indigency_status')) {
                $table->enum('brgy_indigency_status', ['good', 'bad'])->nullable()->after('grade_slip_status');
            }

            // Add comment columns if they don’t exist
            if (!Schema::hasColumn('tbl_renewal', 'cert_of_reg_comment')) {
                $table->text('cert_of_reg_comment')->nullable()->after('brgy_indigency_status');
            }
            if (!Schema::hasColumn('tbl_renewal', 'grade_slip_comment')) {
                $table->text('grade_slip_comment')->nullable()->after('cert_of_reg_comment');
            }
            if (!Schema::hasColumn('tbl_renewal', 'brgy_indigency_comment')) {
                $table->text('brgy_indigency_comment')->nullable()->after('grade_slip_comment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_renewal', function (Blueprint $table) {
            $table->dropColumn([
                'cert_of_reg_status',
                'grade_slip_status',
                'brgy_indigency_status',
                'cert_of_reg_comment',
                'grade_slip_comment',
                'brgy_indigency_comment',
            ]);
        });
    }
};
