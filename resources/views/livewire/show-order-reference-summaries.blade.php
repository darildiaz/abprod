<div>
    <x-filament::modal id="order-references-modal" width="4xl">
        <x-slot name="title">Referencias de Órdenes</x-slot>

        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-4 py-2 border">Order ID</th>
                    <th class="px-4 py-2 border">Product Code</th>
                    <th class="px-4 py-2 border">Size</th>
                    <th class="px-4 py-2 border">Total Quantity</th>
                    <th class="px-4 py-2 border">Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($summaries as $summary)
                    <tr class="border-b">
                        <td class="px-4 py-2 border">{{ $summary->order_id }}</td>
                        <td class="px-4 py-2 border">{{ $summary->product_id }}</td>
                        <td class="px-4 py-2 border">{{ $summary->size_id }}</td>
                        <td class="px-4 py-2 border text-center">{{ $summary->total_quantity }}</td>
                        <td class="px-4 py-2 border text-right">${{ number_format($summary->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::modal>
</div>
