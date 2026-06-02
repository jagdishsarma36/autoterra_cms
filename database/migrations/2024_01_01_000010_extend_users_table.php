<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('microsoft_id')->nullable()->after('google_id');
            $table->string('phone')->nullable()->after('name');
            $table->string('company')->nullable()->after('phone');
            $table->text('address')->nullable()->after('company');
            $table->string('country')->nullable()->after('address');
            $table->string('avatar')->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id', 'microsoft_id', 'phone', 'company',
                'address', 'country', 'avatar',
            ]);
        });
    }
};
