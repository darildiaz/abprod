<x-filament::page>
    {{ $this->table }}

    @livewire('show-order-reference-summaries', [], key('order-references-modal'))
</x-filament::page>