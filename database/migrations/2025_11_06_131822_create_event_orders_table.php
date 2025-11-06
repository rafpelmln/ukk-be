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
        Schema::create('event_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('participant_id');
            $table->uuid('event_id');
            $table->string('order_number')->unique();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2);
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->enum('payment_method', ['transfer', 'qris']);
            $table->uuid('bank_account_id')->nullable(); // untuk transfer bank
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('payment_proof')->nullable(); // bukti bayar upload
            $table->timestamp('expires_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');

            $table->index(['participant_id', 'status']);
            $table->index(['event_id', 'status']);
            $table->index('order_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_orders');
    }
};
