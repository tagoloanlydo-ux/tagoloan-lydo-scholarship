<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblFamilyIntakeSheetsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tbl_family_intake_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_personnel_id');
            $table->string('family_name')->nullable();
            $table->string('address')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city_municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('housing_status')->nullable();
            $table->decimal('monthly_rent', 10, 2)->nullable();
            $table->string('house_structure')->nullable();
            $table->string('house_ownership')->nullable();
            $table->string('lot_ownership')->nullable();
            $table->string('water_source')->nullable();
            $table->string('electricity_source')->nullable();
            $table->string('toilet_type')->nullable();
            $table->text('health_concerns')->nullable();
            $table->string('waste_management')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->decimal('monthly_expenses', 10, 2)->nullable();
            $table->integer('family_members')->nullable();
            $table->boolean('has_senior_citizen')->default(false);
            $table->boolean('has_pwd')->default(false);
            $table->boolean('has_children_in_school')->default(false);
            $table->text('livelihood_sources')->nullable();
            $table->text('government_assistance')->nullable();
            $table->text('community_involvement')->nullable();
            $table->timestamps();

            $table->foreign('application_personnel_id')
                  ->references('application_personnel_id')
                  ->on('tbl_application_personnel')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tbl_family_intake_sheets');
    }
}