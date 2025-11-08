<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('participants_position_request')) {
            return;
        }

        DB::table('participants_position_request')
            ->where('status', 'accepted')
            ->update(['status' => 'approved']);

        DB::statement(<<<SQL
            ALTER TABLE `participants_position_request`
            MODIFY `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
        SQL);
    }

    public function down(): void
    {
        if (!Schema::hasTable('participants_position_request')) {
            return;
        }

        DB::table('participants_position_request')
            ->where('status', 'approved')
            ->update(['status' => 'accepted']);

        DB::statement(<<<SQL
            ALTER TABLE `participants_position_request`
            MODIFY `status` ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'
        SQL);
    }
};
