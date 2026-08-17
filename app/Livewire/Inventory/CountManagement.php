<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\InventoryVariance;
use App\Models\Location;
use App\Models\Product;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class CountManagement extends Component
{
    use WithPagination;

    public $search = '';

    // Legacy Modal State
    public $inventory_id = null;

    public $product_name = '';

    public $location_name = '';

    public $expected_quantity = 0;

    public $counted_quantity = '';

    public $show_modal = false;

    public $notes = '';

    // Fast Scanner Mode State
    public $scanner_mode = false;

    public $current_location_id = null;

    public $current_location_name = '';

    public $current_product_id = null;

    public $current_product_name = '';

    public $current_count = 0;

    public $current_expected = 0;

    public function rules()
    {
        return [
            'counted_quantity' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    // Toggle Fast Scanner Mode
    public function toggleScannerMode()
    {
        $this->scanner_mode = ! $this->scanner_mode;
        if (! $this->scanner_mode) {
            $this->saveCurrentFastCount();
        }
    }

    public function handleScan($barcode)
    {
        if (! $this->scanner_mode) {
            return;
        }

        // Check if barcode is a Location
        $location = Location::where('barcode', $barcode)->first();
        if ($location) {
            $this->saveCurrentFastCount(); // Save previous if exists

            $this->current_location_id = $location->id;
            $this->current_location_name = implode(' / ', array_filter([$location->zone, $location->aisle, $location->rack, $location->shelf, $location->bin]));
            $this->current_product_id = null;
            $this->current_product_name = '';
            $this->current_count = 0;
            $this->current_expected = 0;

            Flux::toast(text: 'Location Locked: '.$location->zone);

            return;
        }

        // Check if barcode is a Product
        $product = Product::where('barcode', $barcode)->orWhere('sku', $barcode)->first();
        if ($product) {
            if (! $this->current_location_id) {
                Flux::toast(variant: 'danger', text: 'Error: Scan a Location first.');

                return;
            }

            if ($this->current_product_id === $product->id) {
                // Same product, increment count
                $this->current_count++;
            } else {
                // Different product, save previous and start new
                $this->saveCurrentFastCount();

                $this->current_product_id = $product->id;
                $this->current_product_name = $product->name.' ('.$product->sku.')';
                $this->current_count = 1;

                // Fetch expected
                $inv = Inventory::where('location_id', $this->current_location_id)
                    ->where('product_id', $this->current_product_id)->first();
                $this->current_expected = $inv ? $inv->quantity : 0;
            }

            return;
        }

        Flux::toast(variant: 'danger', text: 'Barcode not found.');
    }

    public function incrementFastCount()
    {
        $this->current_count++;
    }

    public function decrementFastCount()
    {
        if ($this->current_count > 0) {
            $this->current_count--;
        }
    }

    public function saveCurrentFastCount()
    {
        if ($this->current_location_id && $this->current_product_id && $this->current_count > 0) {
            $inv = Inventory::where('location_id', $this->current_location_id)
                ->where('product_id', $this->current_product_id)->first();

            $expected = $inv ? $inv->quantity : 0;
            $diff = $this->current_count - $expected;

            // TODO: Priority 2 - Replace this direct update with InventoryVariance logic
            if ($diff != 0) {
                InventoryVariance::create([
                    'product_id' => $this->current_product_id,
                    'location_id' => $this->current_location_id,
                    'expected_quantity' => $expected,
                    'counted_quantity' => $this->current_count,
                    'status' => 'pending',
                    'counted_by' => auth()->id(),
                    'notes' => 'Scanner Count Discrepancy',
                ]);
                Flux::toast(variant: 'warning', text: 'Variance logged for Manager Approval');
            } else {
                Flux::toast(variant: 'success', text: 'Count matched for '.$this->current_product_name);
            }

            // Reset product state for next scan
            $this->current_product_id = null;
            $this->current_product_name = '';
            $this->current_count = 0;
            $this->current_expected = 0;
        }
    }

    public function edit($id)
    {
        $inventory = Inventory::with(['product', 'location'])->findOrFail($id);
        $this->inventory_id = $inventory->id;
        $this->product_name = $inventory->product->name.' ('.$inventory->product->sku.')';
        $this->location_name = $inventory->location->zone.'-'.$inventory->location->aisle.'-'.$inventory->location->rack;
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

        InventoryVariance::create([
            'product_id' => $inventory->product_id,
            'location_id' => $inventory->location_id,
            'expected_quantity' => $inventory->quantity,
            'counted_quantity' => $this->counted_quantity,
            'status' => 'pending',
            'counted_by' => auth()->id(),
            'notes' => 'Manual Count Discrepancy: '.$this->notes,
        ]);

        Flux::toast(variant: 'warning', text: __('Variance submitted for Manager Approval.'));
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
            ->whereHas('product', function ($q) {
                $q->when($this->search, function ($q2) {
                    $q2->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
                });
            })
            ->orWhereHas('location', function ($q) {
                $q->when($this->search, function ($q2) {
                    $q2->where('zone', 'like', '%'.$this->search.'%')
                        ->orWhere('barcode', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.inventory.count-management', [
            'stock' => $stock,
        ])->layout('layouts.app');
    }
}
