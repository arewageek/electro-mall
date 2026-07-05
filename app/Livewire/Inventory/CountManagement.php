<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

class CountManagement extends Component
{
    use WithPagination;

    public $search = '';
    
    public $inventory_id = null;
    public $product_name = '';
    public $location_name = '';
    public $expected_quantity = 0;
    
    public $counted_quantity = '';
    public $notes = '';

    public $show_modal = false;

    public function rules()
    {
        return [
            'counted_quantity' => ['required', 'numeric', 'min:0'],
            'notes' => ['required', 'string', 'max:255'],
        ];
    }

    public function edit($id)
    {
        $inventory = Inventory::with(['product', 'location'])->findOrFail($id);
        
        $this->inventory_id = $inventory->id;
        $this->product_name = $inventory->product->name . ' (' . $inventory->product->sku . ')';
        $this->location_name = $inventory->location->zone . '-' . $inventory->location->aisle . '-' . $inventory->location->rack;
        $this->expected_quantity = $inventory->quantity;
        
        $this->counted_quantity = '';
        $this->notes = '';
        
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $inventory = Inventory::findOrFail($this->inventory_id);
        $diff = $this->counted_quantity - $inventory->quantity;

        if ($diff == 0) {
            Flux::toast(variant: 'success', text: __('Count matches expected quantity. No adjustment needed.'));
            $this->show_modal = false;
            return;
        }

        // Apply adjustment
        $inventory->update([
            'quantity' => $this->counted_quantity
        ]);

        // Log transaction
        Transaction::create([
            'product_id' => $inventory->product_id,
            'location_id' => $inventory->location_id,
            'user_id' => auth()->id(),
            'type' => 'count_adjustment',
            'quantity' => $diff,
            'notes' => 'Cycle Count Reconcilation: ' . $this->notes,
        ]);

        Flux::toast(variant: 'success', text: __('Inventory count reconciled and adjusted.'));
        
        $this->show_modal = false;
        $this->reset(['inventory_id', 'product_name', 'location_name', 'expected_quantity', 'counted_quantity', 'notes']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $stock = Inventory::query()
            ->with(['product', 'location'])
            ->whereHas('product', function($q) {
                $q->when($this->search, function($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->orWhereHas('location', function($q) {
                $q->when($this->search, function($q2) {
                    $q2->where('zone', 'like', '%' . $this->search . '%')
                       ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.inventory.count-management', [
            'stock' => $stock,
        ])->layout('layouts.app');
    }
}
