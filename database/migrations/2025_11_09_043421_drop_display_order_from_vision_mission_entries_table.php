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
        Schema::table('vision_mission_entries', function (Blueprint $table) {
            if (Schema::hasColumn('vision_mission_entries', 'display_order')) {
                $table->dropColumn('display_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vision_mission_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('vision_mission_entries', 'display_order')) {
                $table->unsignedInteger('display_order')->default(0);
            }
        });
    }
};
