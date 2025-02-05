<?php
namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ShowOrderReferenceSummaries extends Component
{
    public array $orderIds = [];
    public array $summaries = [];
    public bool $isOpen = false; // Control para el modal

    #[On('openOrderReferencesModal')]
    public function loadSummaries(array $orderIds)
    {
        $this->orderIds = $orderIds;
        $this->isOpen = true; // Abre el modal

        $this->summaries = DB::table('order_reference_summaries')
            ->whereIn('order_id', $this->orderIds)
            ->get()
            ->toArray();
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.show-order-reference-summaries');
    }
}
