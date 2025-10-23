<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_intake_sheets', function (Blueprint $table) {
            $table->id();

            /**
             * RELATIONSHIPS
             * -----------------------------
             * Links this intake sheet to an applicant and a LYDO staff.
             */
            $table->foreignId('application_personnel_id')
                  ->constrained('tbl_application_personnel')
                  ->onDelete('cascade');

            $table->foreignId('lydo_personnel_id')
                  ->constrained('tbl_lydo_personnel')
                  ->onDelete('set null')
                  ->nullable();

            /**
             * HEAD OF FAMILY (extra info only)
             * -----------------------------
             * Core applicant details (name, barangay, etc.) remain in tbl_application_personnel.
             */
            $table->string('head_4ps')->nullable();
            $table->string('head_ipno')->nullable();
            $table->string('head_address')->nullable();
            $table->string('head_zone')->nullable();
            $table->date('head_dob')->nullable();
            $table->string('head_pob')->nullable();
            $table->string('head_educ')->nullable();
            $table->string('head_occ')->nullable();
            $table->string('head_religion')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();

            /**
             * HOUSEHOLD INFORMATION
             * -----------------------------
             */
            $table->string('house_total_income')->nullable();
            $table->string('house_net_income')->nullable();
            $table->string('other_income')->nullable();
            $table->string('house_house')->nullable();
            $table->string('house_lot')->nullable();
            $table->string('house_water')->nullable();
            $table->string('house_electric')->nullable();

            /**
             * FAMILY MEMBERS (JSON)
             * -----------------------------
             * Stored as JSON for flexibility.
             */
            $table->json('family_members')->nullable()
                  ->comment('JSON array of family members (name, relation, birth, age, sex, civil status, education, occupation, income, remarks)');

            /**
             * SOCIAL SERVICE RECORDS (JSON)
             * -----------------------------
             */
            $table->json('social_service_records')->nullable()
                  ->comment('JSON array of service records (date, problem, assistance, remarks)');

            /**
             * HEALTH CONDITION + SIGNATURES
             * -----------------------------
             */
            $table->decimal('hc_estimated_cost', 10, 2)->nullable();
            $table->string('worker_name')->nullable();
            $table->string('officer_name')->nullable();
            $table->date('date_entry')->nullable();
            $table->longText('signature_client')->nullable();
            $table->longText('signature_worker')->nullable();
            $table->longText('signature_officer')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_intake_sheets');
    }
};
