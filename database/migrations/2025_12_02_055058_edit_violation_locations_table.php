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
         Schema::table('violation_locations', function (Blueprint $table) {

            if (!Schema::hasColumn('violation_locations', 'city_id')) {
                $table->foreignId('city_id')
                      ->nullable()
                      ->constrained('cities')
                      ->cascadeOnUpdate()
                      ->restrictOnDelete()
                      ->after('id');
            }

            if (Schema::hasColumn('violation_locations', 'steet_name')) {
                $table->renameColumn('steet_name', 'street_name');
            }

            if (Schema::hasColumn('violation_locations', 'land_mark')) {
                $table->renameColumn('land_mark', 'landmark');
            }

            if (Schema::hasColumn('violation_locations', 'address')) {
                $table->dropColumn('address');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
