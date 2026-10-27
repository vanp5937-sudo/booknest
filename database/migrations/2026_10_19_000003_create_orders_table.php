<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_code', 30)->unique();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone', 30);
            $table->text('address');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('status', 30)->default('pending');
            $table->string('payment_method', 30)->default('cod');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
