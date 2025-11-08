<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('event_orders', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('paid_at');
            }
        });

        DB::statement("ALTER TABLE event_orders MODIFY COLUMN status ENUM('pending','paid','completed','expired','cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_orders', function (Blueprint $table) {
            if (Schema::hasColumn('event_orders', 'checked_in_at')) {
                $table->dropColumn('checked_in_at');
            }
        });

        DB::statement("ALTER TABLE event_orders MODIFY COLUMN status ENUM('pending','paid','expired','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
