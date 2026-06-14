@php
    $record = $getRecord();
    $invoices = $record->invoices ?? [];
@endphp

@if(!empty($invoices))
<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5">
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $inv)
                @php
                    $invStatus = $inv['status'] ?? 'unknown';
                    $invAmount = $inv['amount'] ?? 0;
                    $invCurrency = $inv['currency'] ?? 'INR';
                    $invCreated = isset($inv['created_at']) ? \Carbon\Carbon::createFromTimestamp($inv['created_at'])->format('M j, Y') : 'N/A';
                    $invShortId = substr($inv['id'] ?? '', -8);
                    $paidAt = isset($inv['paid_at']) ? \Carbon\Carbon::createFromTimestamp($inv['paid_at'])->format('M j, Y g:i A') : null;
                @endphp
                <tr class="border-b border-gray-100 dark:border-white/5 last:border-0">
                    <td class="px-4 py-3 font-mono text-xs font-semibold">INV-{{ strtoupper($invShortId) }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $invCreated }}</td>
                    <td class="px-4 py-3 font-semibold">
                        {{ $invCurrency === 'INR' ? '₹' . number_format($invAmount / 100, 0) : '$' . number_format($invAmount / 100, 2) }}
                    </td>
                    <td class="px-4 py-3">
                        @if($invStatus === 'paid')
                            <span class="inline-flex items-center rounded-full bg-success-50 dark:bg-success-500/10 px-2 py-1 text-xs font-semibold text-success-700 dark:text-success-500 ring-1 ring-inset ring-success-600/20 dark:ring-success-500/20">Paid</span>
                        @elseif($invStatus === 'failed')
                            <span class="inline-flex items-center rounded-full bg-danger-50 dark:bg-danger-500/10 px-2 py-1 text-xs font-semibold text-danger-700 dark:text-danger-500 ring-1 ring-inset ring-danger-600/20 dark:ring-danger-500/20">Failed</span>
                        @elseif($invStatus === 'created')
                            <span class="inline-flex items-center rounded-full bg-warning-50 dark:bg-warning-500/10 px-2 py-1 text-xs font-semibold text-warning-700 dark:text-warning-500 ring-1 ring-inset ring-warning-600/20 dark:ring-warning-500/20">Pending</span>
                        @elseif($invStatus === 'cancelled')
                            <span class="inline-flex items-center rounded-full bg-gray-50 dark:bg-gray-500/10 px-2 py-1 text-xs font-semibold text-gray-700 dark:text-gray-500 ring-1 ring-inset ring-gray-600/20 dark:ring-gray-500/20">Cancelled</span>
                        @elseif($invStatus === 'expired')
                            <span class="inline-flex items-center rounded-full bg-gray-50 dark:bg-gray-500/10 px-2 py-1 text-xs font-semibold text-gray-700 dark:text-gray-500 ring-1 ring-inset ring-gray-600/20 dark:ring-gray-500/20">Expired</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-50 dark:bg-gray-500/10 px-2 py-1 text-xs font-semibold text-gray-700 dark:text-gray-500 ring-1 ring-inset ring-gray-600/20 dark:ring-gray-500/20">{{ ucfirst($invStatus) }}</span>
                        @endif
                        @if($invStatus === 'paid' && $paidAt)
                            <span class="ml-2 text-xs text-gray-400">— {{ $paidAt }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class="text-sm text-gray-500 dark:text-gray-400 italic">No invoices found.</p>
@endif
