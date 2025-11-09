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
            if (!Schema::hasColumn('leadership_structures', 'generation_id')) {
                $table->uuid('generation_id')->nullable()->after('id');
                $table->foreign('generation_id')
                    ->references('id')
                    ->on('generations')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leadership_structures', function (Blueprint $table) {
            if (Schema::hasColumn('leadership_structures', 'generation_id')) {
                $table->dropForeign(['generation_id']);
                $table->dropColumn('generation_id');
            }
        });
    }
};
