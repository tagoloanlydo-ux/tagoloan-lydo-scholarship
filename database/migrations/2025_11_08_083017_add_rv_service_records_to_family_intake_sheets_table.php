<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('family_intake_sheets', function (Blueprint $table) {
        $table->text('rv_service_records')->nullable()->after('social_service_records');
    });
}

public function down()
{
    Schema::table('family_intake_sheets', function (Blueprint $table) {
        $table->dropColumn('rv_service_records');
    });
}
};
