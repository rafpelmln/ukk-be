<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_position', function (Blueprint $table) {
            $table->uuid('activity_id');
            $table->uuid('position_id');
            $table->primary(['activity_id', 'position_id']);
            $table->foreign('activity_id')->references('id')->on('activities')->cascadeOnDelete();
            $table->foreign('position_id')->references('id')->on('positions')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_position');
    }
};
