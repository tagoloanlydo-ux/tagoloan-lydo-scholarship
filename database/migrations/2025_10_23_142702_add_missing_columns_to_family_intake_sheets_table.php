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
        Schema::table('family_intake_sheets', function (Blueprint $table) {
            $table->string('head_barangay')->nullable()->after('head_zone');
            $table->string('house_value')->nullable()->after('house_lot');
            $table->string('lot_value')->nullable()->after('house_value');
            $table->string('house_rent')->nullable()->after('lot_value');
            $table->string('lot_rent')->nullable()->after('house_rent');
            $table->string('house_remarks')->nullable()->after('house_electric');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_intake_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'head_barangay',
                'head_pob',
                'house_value',
                'lot_value',
                'house_rent',
                'lot_rent',
                'house_remarks'
            ]);
        });
    }
};
