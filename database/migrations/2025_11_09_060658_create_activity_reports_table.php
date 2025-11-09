<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('activity_id');
            $table->uuid('participant_id');
            $table->enum('status', ['present', 'excused', 'absent'])->default('present');
            $table->timestamp('checked_in_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['activity_id', 'participant_id']);
            $table->foreign('activity_id')->references('id')->on('activities')->cascadeOnDelete();
            $table->foreign('participant_id')->references('id')->on('participants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_reports');
    }
};
