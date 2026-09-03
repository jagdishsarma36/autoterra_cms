<x-filament-panels::page>

    @if(empty($cart))

        <div class="flex flex-col items-center justify-center py-20 text-center">

            <x-heroicon-o-shopping-cart
                class="w-16 h-16 text-gray-400"
            />

            <h2 class="mt-4 text-xl font-bold">
                Your cart is empty
            </h2>

            <p class="mt-2 text-gray-500">
                Add a product to your cart to continue.
            </p>

        </div>

    @else

        <div class="space-y-6">

            {{-- Cart Items --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 dark:bg-gray-800">

                            <tr>

                                <th class="px-6 py-4 text-left">
                                    Product
                                </th>

                                <th class="px-6 py-4 text-left">
                                    Term
                                </th>

                                <th class="px-6 py-4 text-right">
                                    Unit Price
                                </th>

                                <th class="px-6 py-4 text-center">
                                    Quantity
                                </th>

                                <th class="px-6 py-4 text-right">
                                    Total
                                </th>

                                <th class="px-6 py-4">
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                            @foreach($cart as $item)

                                <tr wire:key="cart-{{ $item['key'] }}">

                                    {{-- Product --}}

                                    <td class="px-6 py-5">

                                        <div class="font-medium">
                                            {{ $item['product_name'] }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            SKU: {{ $item['sku'] }}
                                        </div>

                                    </td>

                                    {{-- Term --}}

                                    <td class="px-6 py-5">

                                        <span class="rounded-md bg-gray-100 px-2 py-1 text-xs dark:bg-gray-800">

                                            {{ ucfirst($item['term']) }}

                                        </span>

                                    </td>

                                    {{-- Unit Price --}}

                                    <td class="px-6 py-5 text-right">

                                        {{ $item['currency'] }}

                                        {{ number_format($item['unit_price'], 2) }}

                                    </td>

                                    {{-- Quantity --}}

                                    <td class="px-6 py-5">

                                        <div class="flex items-center justify-center gap-2">

                                            <button
                                                type="button"
                                                wire:click="decrement('{{ $item['key'] }}')"
                                                class="rounded-lg border px-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-800"
                                            >
                                                −
                                            </button>

                                            <span class="w-8 text-center">
                                                {{ $item['quantity'] }}
                                            </span>

                                            <button
                                                type="button"
                                                wire:click="increment('{{ $item['key'] }}')"
                                                class="rounded-lg border px-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-800"
                                            >
                                                +
                                            </button>

                                        </div>

                                    </td>

                                    {{-- Total --}}

                                    <td class="px-6 py-5 text-right font-semibold">

                                        {{ $item['currency'] }}

                                        {{ number_format(
                                            $item['unit_price'] * $item['quantity'],
                                            2
                                        ) }}

                                    </td>

                                    {{-- Remove --}}

                                    <td class="px-6 py-5 text-center">

                                        <button
                                            type="button"
                                            wire:click="remove('{{ $item['key'] }}')"
                                            class="text-danger-600 hover:text-danger-800"
                                            title="Remove"
                                        >

                                            <x-heroicon-o-trash
                                                class="h-5 w-5"
                                            />

                                        </button>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Summary --}}

            <div class="flex justify-end">

                <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">

                    <div class="flex justify-between text-lg">

                        <span>
                            Items
                        </span>

                        <span>
                            {{ $this->getCartCount() }}
                        </span>

                    </div>


                    <div class="mt-4 flex justify-between border-t pt-4 text-xl font-bold">

                        <span>
                            Total
                        </span>

                        <span>
                            ₹{{ number_format($this->getSubtotal(), 2) }}
                        </span>

                    </div>


                    <button
                        type="button"
                        class="mt-6 w-full rounded-lg bg-primary-600 px-4 py-3 text-center font-semibold text-white hover:bg-primary-700"
                    >
                        Proceed to Checkout
                    </button>

                </div>

            </div>

        </div>

    @endif

</x-filament-panels::page>