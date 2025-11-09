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
        Schema::create('leadership_structures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('period_label', 100);
            $table->string('period_year', 50);
            $table->boolean('is_active')->default(false)->index();
            $table->string('general_leader_name', 100);
            $table->string('general_leader_photo_path')->nullable();
            $table->string('leader_one_name', 100)->nullable();
            $table->string('leader_one_photo_path')->nullable();
            $table->string('leader_two_name', 100)->nullable();
            $table->string('leader_two_photo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leadership_structures');
    }
};
