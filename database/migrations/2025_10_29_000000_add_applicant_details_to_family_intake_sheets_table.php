<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_intake_sheets', function (Blueprint $table) {
            $table->string('applicant_fname')->nullable()->after('lydo_personnel_id');
            $table->string('applicant_mname')->nullable()->after('applicant_fname');
            $table->string('applicant_lname')->nullable()->after('applicant_mname');
            $table->string('applicant_suffix')->nullable()->after('applicant_lname');
            $table->string('applicant_gender')->nullable()->after('applicant_suffix');
        });
    }

    public function down(): void
    {
        Schema::table('family_intake_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'applicant_fname',
                'applicant_mname',
                'applicant_lname',
                'applicant_suffix',
                'applicant_gender',
            ]);
        });
    }
};
