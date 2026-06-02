<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('term'); // 3mo, 6mo, 1yr, 3yr, 5yr
            $table->bigInteger('price_inr')->nullable(); // in paise
            $table->integer('price_usd')->nullable(); // in cents
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
