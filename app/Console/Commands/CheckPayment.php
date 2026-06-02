<?php

namespace App\Console\Commands;

use App\Services\RazorpayService;
use Illuminate\Console\Command;

class CheckPayment extends Command
{
    protected $signature = 'razorpay:check-payment {paymentId : Razorpay payment ID (e.g. pay_xxx)}';

    protected $description = 'Fetch payment details from Razorpay API';

    public function handle(RazorpayService $razorpay): int
    {
        $paymentId = $this->argument('paymentId');

        $this->info("Fetching payment: {$paymentId}...");

        $result = $razorpay->fetchPayment($paymentId);

        if (isset($result['error'])) {
            $this->error("Error: {$result['error']}");
            return 1;
        }

        $this->newLine();
        $this->table(['Field', 'Value'], [
            ['ID', $result['id'] ?? ''],
            ['Status', $result['status'] ?? ''],
            ['Amount', ($result['amount'] ?? 0) / 100 . ' ' . ($result['currency'] ?? 'INR')],
            ['Method', $result['method'] ?? ''],
            ['Description', $result['description'] ?? ''],
            ['Order ID', $result['order_id'] ?? ''],
            ['Invoice ID', $result['invoice_id'] ?? ''],
            ['Created at', $result['created_at'] ? date('Y-m-d H:i:s', $result['created_at']) : ''],
            ['Captured', ($result['captured'] ?? false) ? 'Yes' : 'No'],
            ['Fee', isset($result['fee']) ? ($result['fee'] / 100) . ' ' . ($result['currency'] ?? 'INR') : ''],
            ['Notes', json_encode($result['notes'] ?? [], JSON_PRETTY_PRINT)],
        ]);

        return 0;
    }
}
