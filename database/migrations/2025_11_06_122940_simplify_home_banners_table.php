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
        Schema::table('home_banners', function (Blueprint $table) {
            if (Schema::hasColumn('home_banners', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('home_banners', 'subtitle')) {
                $table->dropColumn('subtitle');
            }
            if (Schema::hasColumn('home_banners', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('home_banners', 'button_label')) {
                $table->dropColumn('button_label');
            }
            if (Schema::hasColumn('home_banners', 'button_url')) {
                $table->dropColumn('button_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            if (!Schema::hasColumn('home_banners', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('home_banners', 'subtitle')) {
                $table->string('subtitle')->nullable();
            }
            if (!Schema::hasColumn('home_banners', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('home_banners', 'button_label')) {
                $table->string('button_label')->nullable();
            }
            if (!Schema::hasColumn('home_banners', 'button_url')) {
                $table->string('button_url')->nullable();
            }
        });
    }
};
