<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page'); // home, about, products, pricing, buy, contact, quote, pro, pro_spatial, solutions, resources, global
            $table->string('key');  // hero.heading, hero.description, faq.items, etc.
            $table->longText('value')->nullable(); // plain text, HTML, or JSON
            $table->enum('type', ['text', 'richtext', 'json'])->default('text');
            $table->timestamps();

            $table->unique(['page', 'key']);
            $table->index('page');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};
