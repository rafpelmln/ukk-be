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
        Schema::table('positions', function (Blueprint $table) {
            // Rename existing 'name' column to 'slug'
            $table->renameColumn('name', 'slug');
        });

        // Add new 'name' column after 'slug'
        Schema::table('positions', function (Blueprint $table) {
            $table->string('name')->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            // Drop the new 'name' column
            $table->dropColumn('name');
        });

        Schema::table('positions', function (Blueprint $table) {
            // Rename 'slug' back to 'name'
            $table->renameColumn('slug', 'name');
        });
    }
};
