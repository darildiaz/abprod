<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">Sales Goals Progress</h2>

        <!-- Month Filter Dropdown -->
        <div class="mb-4">
            <label for="month" class="block text-sm font-medium text-gray-700">Select Month</label>
            <select wire:model="month" id="month" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @foreach ([
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ] as $num => $name)
                    <option value="{{ $num }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Render Chart -->
        {{ $this->chart }}
    </x-filament::card>
</x-filament::widget>
