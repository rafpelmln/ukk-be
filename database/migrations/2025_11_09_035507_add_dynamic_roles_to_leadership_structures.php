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
        Schema::table('leadership_structures', function (Blueprint $table) {
            if (Schema::hasColumn('leadership_structures', 'leader_one_name')) {
                $table->dropColumn(['leader_one_name', 'leader_one_photo_path']);
            }

            if (Schema::hasColumn('leadership_structures', 'leader_two_name')) {
                $table->dropColumn(['leader_two_name', 'leader_two_photo_path']);
            }
        });

        Schema::create('leadership_structure_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('leadership_structure_id');
            $table->string('title', 100);
            $table->string('person_name', 100);
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();

            $table->foreign('leadership_structure_id')
                ->references('id')
                ->on('leadership_structures')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leadership_structure_roles');

        Schema::table('leadership_structures', function (Blueprint $table) {
            if (!Schema::hasColumn('leadership_structures', 'leader_one_name')) {
                $table->string('leader_one_name', 100)->nullable();
                $table->string('leader_one_photo_path')->nullable();
            }

            if (!Schema::hasColumn('leadership_structures', 'leader_two_name')) {
                $table->string('leader_two_name', 100)->nullable();
                $table->string('leader_two_photo_path')->nullable();
            }
        });
    }
};
