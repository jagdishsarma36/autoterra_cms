<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->text('description')->nullable();
            $table->string('folder')->default('/');
            $table->timestamps();

            $table->index('folder');
            $table->index('mime_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
