<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('activities', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }

            if (!Schema::hasColumn('activities', 'target_scope')) {
                $table->enum('target_scope', ['all', 'positions'])->default('all')->after('datetime');
            }

            if (!Schema::hasColumn('activities', 'status')) {
                $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled')->after('target_scope');
            }
        });

        // Backfill slug values for existing records
        DB::table('activities')->whereNull('slug')->orderBy('created_at')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $base = Str::slug($row->name ?? 'kegiatan');
                $slug = $base;
                $counter = 1;

                while (DB::table('activities')->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $counter++;
                }

                DB::table('activities')->where('id', $row->id)->update(['slug' => $slug]);
            }
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->string('slug')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('activities', 'target_scope')) {
                $table->dropColumn('target_scope');
            }

            if (Schema::hasColumn('activities', 'slug')) {
                $table->dropUnique('activities_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};
