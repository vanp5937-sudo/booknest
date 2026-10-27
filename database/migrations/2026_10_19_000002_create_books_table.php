<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('title');
            $table->string('slug', 280)->unique();
            $table->string('author', 180);
            $table->string('publisher', 180)->nullable();
            $table->string('isbn', 20)->nullable()->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->index(['category_id', 'status']);
            $table->index('title');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
