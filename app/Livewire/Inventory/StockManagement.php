<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

class StockManagement extends Component
{
    use WithPagination;

    public $search = '';
    
    public $inventory_id = null;
    public $product_id = '';
    public $location_id = '';
    public $quantity = 0;

    public $is_editing = false;
    public $show_modal = false;

    public function rules()
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function create()
    {
        $this->reset(['inventory_id', 'product_id', 'location_id', 'quantity']);
        $this->is_editing = false;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        
        $this->inventory_id = $inventory->id;
        $this->product_id = $inventory->product_id;
        $this->location_id = $inventory->location_id;
        $this->quantity = $inventory->quantity;
        
        $this->is_editing = true;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'product_id' => $this->product_id,
            'location_id' => $this->location_id,
            'quantity' => $this->quantity,
        ];

        // Check if there's already an inventory record for this product at this location
        // If it's a new record or moving to a location that already has it, we might want to consolidate
        $existing = Inventory::where('product_id', $this->product_id)
            ->where('location_id', $this->location_id)
            ->when($this->inventory_id, function($q) {
                $q->where('id', '!=', $this->inventory_id);
            })
            ->first();

        if ($existing) {
            // Consolidate quantity
            $existing->increment('quantity', $this->quantity);
            if ($this->inventory_id) {
                Inventory::findOrFail($this->inventory_id)->delete();
            }
            Flux::toast(variant: 'success', text: __('Stock consolidated successfully.'));
        } else {
            if ($this->inventory_id) {
                Inventory::findOrFail($this->inventory_id)->update($data);
                Flux::toast(variant: 'success', text: __('Stock updated successfully.'));
            } else {
                Inventory::create($data);
                Flux::toast(variant: 'success', text: __('Stock added successfully.'));
            }
        }

        $this->show_modal = false;
        $this->reset(['inventory_id', 'product_id', 'location_id', 'quantity']);
    }
    
    public function delete($id)
    {
        Inventory::findOrFail($id)->delete();
        Flux::toast(variant: 'success', text: __('Stock record deleted.'));
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

        return view('livewire.inventory.stock-management', [
            'stock' => $stock,
            'products' => Product::orderBy('name')->get(),
            'locations' => Location::orderBy('zone')->orderBy('aisle')->orderBy('rack')->get(),
        ])->layout('layouts.app');
    }
}
