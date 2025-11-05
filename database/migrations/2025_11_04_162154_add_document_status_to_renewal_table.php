<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_renewal', function (Blueprint $table) {
            $table->enum('cert_of_reg_status', ['good', 'bad'])->nullable()->after('rejection_reason');
            $table->text('cert_of_reg_comment')->nullable()->after('cert_of_reg_status');
            $table->enum('grade_slip_status', ['good', 'bad'])->nullable()->after('cert_of_reg_comment');
            $table->text('grade_slip_comment')->nullable()->after('grade_slip_status');
            $table->enum('brgy_indigency_status', ['good', 'bad'])->nullable()->after('grade_slip_comment');
            $table->text('brgy_indigency_comment')->nullable()->after('brgy_indigency_status');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_renewal', function (Blueprint $table) {
            $table->dropColumn([
                'cert_of_reg_status',
                'cert_of_reg_comment',
                'grade_slip_status', 
                'grade_slip_comment',
                'brgy_indigency_status',
                'brgy_indigency_comment'
            ]);
        });
    }
};