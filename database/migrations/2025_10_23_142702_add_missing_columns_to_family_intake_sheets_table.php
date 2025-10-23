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
            $table->string('head_pob')->nullable()->after('head_barangay');
            $table->string('house_house_value')->nullable()->after('house_lot');
            $table->string('house_lot_value')->nullable()->after('house_house_value');
            $table->string('house_house_rent')->nullable()->after('house_lot_value');
            $table->string('house_lot_rent')->nullable()->after('house_house_rent');
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
                'house_house_value',
                'house_lot_value',
                'house_house_rent',
                'house_lot_rent',
                'house_remarks'
            ]);
        });
    }
};
