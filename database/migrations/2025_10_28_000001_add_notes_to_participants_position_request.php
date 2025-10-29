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
        Schema::table('participants_position_request', function (Blueprint $table) {
            if (!Schema::hasColumn('participants_position_request', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('participants_position_request', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants_position_request', function (Blueprint $table) {
            if (Schema::hasColumn('participants_position_request', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
            if (Schema::hasColumn('participants_position_request', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
