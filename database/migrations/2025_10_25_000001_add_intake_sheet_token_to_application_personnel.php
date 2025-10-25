<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIntakeSheetTokenToApplicationPersonnel extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tbl_application_personnel', function (Blueprint $table) {
            $table->timestamp('intake_sheet_token_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tbl_application_personnel', function (Blueprint $table) {
            $table->dropColumn('intake_sheet_token');
            $table->dropColumn('intake_sheet_token_expires_at');
        });
    }
}