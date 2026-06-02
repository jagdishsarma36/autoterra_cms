<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->integer('billing_cycles')->nullable()->after('is_active')
                ->comment('Number of billing cycles for subscriptions (e.g. 12 for monthly×12)');
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropColumn('billing_cycles');
        });
    }
};
