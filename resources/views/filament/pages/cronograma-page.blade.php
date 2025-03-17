<x-filament::page>
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{ $this->table }}

    @livewire('show-order-reference-summaries', [], key('order-references-modal'))
</x-filament::page>