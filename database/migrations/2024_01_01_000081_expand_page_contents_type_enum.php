<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement("ALTER TABLE page_contents RENAME COLUMN `type` TO `type_old`");
            DB::statement("ALTER TABLE page_contents ADD COLUMN `type` VARCHAR(255) NOT NULL DEFAULT 'text'");
            DB::statement("UPDATE page_contents SET `type` = `type_old`");
            DB::statement("ALTER TABLE page_contents DROP COLUMN `type_old`");
        } else {
            DB::statement("
                ALTER TABLE page_contents
                MODIFY COLUMN `type` VARCHAR(255) NOT NULL DEFAULT 'text'
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement("ALTER TABLE page_contents RENAME COLUMN `type` TO `type_old`");
            DB::statement("ALTER TABLE page_contents ADD COLUMN `type` VARCHAR(255) NOT NULL DEFAULT 'text'");
            DB::statement("UPDATE page_contents SET `type` = `type_old`");
            DB::statement("ALTER TABLE page_contents DROP COLUMN `type_old`");
        } else {
            DB::statement("
                ALTER TABLE page_contents
                MODIFY COLUMN `type`
                ENUM('text','richtext','json')
                NOT NULL DEFAULT 'text'
            ");
        }
    }
};