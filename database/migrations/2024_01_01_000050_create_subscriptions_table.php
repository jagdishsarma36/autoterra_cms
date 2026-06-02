<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('term');
            $table->string('razorpay_subscription_id')->nullable();
            $table->string('razorpay_plan_id')->nullable();
            $table->enum('status', ['active', 'cancelled', 'expired', 'paused'])->default('active');
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->bigInteger('amount');
            $table->string('currency', 3)->default('INR');
            $table->timestamps();

            $table->index('razorpay_subscription_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
