<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite cannot ALTER COLUMN type, so we recreate the table
        DB::statement('CREATE TABLE page_contents_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page VARCHAR NOT NULL,
            "key" VARCHAR NOT NULL,
            value TEXT,
            type VARCHAR NOT NULL DEFAULT \'text\',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');

        DB::statement("INSERT INTO page_contents_new (id, page, \"key\", value, type, created_at, updated_at) SELECT id, page, \"key\", value, type, created_at, updated_at FROM page_contents");

        DB::statement('DROP TABLE page_contents');
        DB::statement('ALTER TABLE page_contents_new RENAME TO page_contents');

        Schema::table('page_contents', function (Blueprint $table) {
            $table->unique(['page', 'key']);
            $table->index('page');
        });
    }

    public function down(): void
    {
        DB::statement('CREATE TABLE page_contents_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page VARCHAR NOT NULL,
            "key" VARCHAR NOT NULL,
            value TEXT,
            type VARCHAR NOT NULL DEFAULT \'text\',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');

        DB::statement("INSERT INTO page_contents_new (id, page, \"key\", value, type, created_at, updated_at) SELECT id, page, \"key\", value, type, created_at, updated_at FROM page_contents WHERE type IN ('text', 'richtext', 'json')");

        DB::statement('DROP TABLE page_contents');
        DB::statement('ALTER TABLE page_contents_new RENAME TO page_contents');

        Schema::table('page_contents', function (Blueprint $table) {
            $table->unique(['page', 'key']);
            $table->index('page');
        });
    }
};
