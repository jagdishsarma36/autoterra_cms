<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Subscription;
use App\Services\RazorpayService;
use Illuminate\Console\Command;

class SyncPayment extends Command
{
    protected $signature = 'razorpay:sync-payment {paymentId : Razorpay payment ID (e.g. pay_xxx)}';

    protected $description = 'Check a Razorpay payment and sync it to the database';

    public function handle(RazorpayService $razorpay): int
    {
        $paymentId = $this->argument('paymentId');

        $this->info("Fetching payment: {$paymentId} from Razorpay...");

        $result = $razorpay->fetchPayment($paymentId);

        if (isset($result['error'])) {
            $this->error("Razorpay API error: {$result['error']}");
            return 1;
        }

        $status = $result['status'] ?? 'unknown';
        $amount = ($result['amount'] ?? 0) / 100;
        $currency = $result['currency'] ?? 'INR';
        $orderId = $result['order_id'] ?? '';
        $method = $result['method'] ?? '';
        $description = $result['description'] ?? '';
        $createdAt = $result['created_at'] ? date('Y-m-d H:i:s', $result['created_at']) : '';

        $this->newLine();
        $this->info("Payment Details:");
        $this->table(['Field', 'Value'], [
            ['Payment ID', $paymentId],
            ['Status', $status],
            ['Amount', "{$amount} {$currency}"],
            ['Method', $method],
            ['Order ID', $orderId],
            ['Description', $description],
            ['Created at', $createdAt],
        ]);

        // Check if this payment is already in our orders
        $existingOrder = Order::where('razorpay_payment_id', $paymentId)->first();
        if ($existingOrder) {
            $this->newLine();
            $this->info("Already synced in orders table:");
            $this->table(['Field', 'Value'], [
                ['Order ID', $existingOrder->id],
                ['Status', $existingOrder->status],
                ['Amount', $existingOrder->total_amount],
                ['Product', $existingOrder->product->name ?? 'N/A'],
                ['User', $existingOrder->user->email ?? 'N/A'],
            ]);
            return 0;
        }

        // Check if there's a pending order with this razorpay_order_id
        if ($orderId) {
            $pendingOrder = Order::where('razorpay_order_id', $orderId)->first();
            if ($pendingOrder) {
                $this->newLine();
                $this->info("Found pending order #{$pendingOrder->id} with this Razorpay order.");

                if ($status === 'captured' || $status === 'authorized') {
                    if ($this->confirm("Update order #{$pendingOrder->id} status to 'paid'?", true)) {
                        $pendingOrder->update([
                            'razorpay_payment_id' => $paymentId,
                            'status' => 'paid',
                        ]);
                        $this->info("Order #{$pendingOrder->id} updated to 'paid'.");
                    }
                } else {
                    $this->warn("Payment status is '{$status}', not updating order.");
                }
                return 0;
            }
        }

        $this->newLine();
        $this->warn("No matching order found in database for this payment.");

        if ($status === 'captured' || $status === 'authorized') {
            $this->info("Payment was captured on Razorpay but not recorded in your app.");
            $this->line("This likely means the verify callback failed or was never called.");
        }

        return 0;
    }
}
