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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->integer('amount');
            $table->enum('status', ['PENDING', 'PAID', 'EXPIRED', 'CANCELLED'])->default('PENDING');
            $table->string('transaction_id')->nullable();
            $table->text('midtrans_token')->nullable();
            $table->text('midtrans_redirect_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
