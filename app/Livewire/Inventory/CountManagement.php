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
    public $show_modal = false;

    // Scanner Workflow State
    public $show_scanner_modal = false;
    public $scan_location_barcode = '';
    public $scan_product_barcode = '';
    public $scanned_location_id = null;
    public $scanned_product_id = null;
    public $scanned_product_name = '';
    public $scanned_location_name = '';
    public $scanned_expected_quantity = 0;

    public function rules()
    {
        return [
            'counted_quantity' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
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

    public function openScanner()
    {
        $this->reset(['scan_location_barcode', 'scan_product_barcode', 'scanned_location_id', 'scanned_product_id', 'scanned_product_name', 'scanned_location_name', 'scanned_expected_quantity', 'counted_quantity', 'notes', 'inventory_id']);
        $this->show_scanner_modal = true;
    }

    public function resolveScanLocation()
    {
        $barcode = $this->scan_location_barcode;
        if (empty($barcode)) return;

        $location = \App\Models\Location::where('barcode', $barcode)->first();
        if ($location) {
            $this->scanned_location_id = $location->id;
            $this->scanned_location_name = implode(' / ', array_filter([$location->zone, $location->aisle, $location->rack, $location->shelf, $location->bin]));
            $this->resetErrorBag('scan_location_barcode');
            $this->checkScannedInventory();
        } else {
            $this->addError('scan_location_barcode', __('Invalid location barcode.'));
            $this->scanned_location_id = null;
            $this->scanned_location_name = '';
        }
    }

    public function resolveScanProduct()
    {
        $barcode = $this->scan_product_barcode;
        if (empty($barcode)) return;

        $product = \App\Models\Product::where('barcode', $barcode)->orWhere('sku', $barcode)->first();
        if ($product) {
            $this->scanned_product_id = $product->id;
            $this->scanned_product_name = $product->name . ' (' . $product->sku . ')';
            $this->resetErrorBag('scan_product_barcode');
            $this->checkScannedInventory();
        } else {
            $this->addError('scan_product_barcode', __('Invalid product barcode.'));
            $this->scanned_product_id = null;
            $this->scanned_product_name = '';
        }
    }

    public function checkScannedInventory()
    {
        if ($this->scanned_location_id && $this->scanned_product_id) {
            $inventory = Inventory::where('location_id', $this->scanned_location_id)
                ->where('product_id', $this->scanned_product_id)
                ->first();
                
            if ($inventory) {
                $this->inventory_id = $inventory->id;
                $this->scanned_expected_quantity = $inventory->quantity;
            } else {
                $this->inventory_id = null;
                $this->scanned_expected_quantity = 0;
            }
        }
    }

    public function saveScannerCount()
    {
        $this->validate([
            'counted_quantity' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'scanned_location_id' => ['required'],
            'scanned_product_id' => ['required'],
        ], [
            'scanned_location_id.required' => 'You must scan a valid location.',
            'scanned_product_id.required' => 'You must scan a valid product.'
        ]);

        $diff = $this->counted_quantity - $this->scanned_expected_quantity;

        if ($diff == 0) {
            Flux::toast(variant: 'success', text: __('Count matches expected quantity. No adjustment needed.'));
            $this->show_scanner_modal = false;
            return;
        }

        if ($this->inventory_id) {
            $inventory = Inventory::find($this->inventory_id);
            $inventory->update(['quantity' => $this->counted_quantity]);
        } else {
            $inventory = Inventory::create([
                'product_id' => $this->scanned_product_id,
                'location_id' => $this->scanned_location_id,
                'quantity' => $this->counted_quantity
            ]);
        }

        Transaction::create([
            'product_id' => $inventory->product_id,
            'location_id' => $inventory->location_id,
            'user_id' => auth()->id(),
            'type' => 'count_adjustment',
            'quantity' => $diff,
            'notes' => 'Cycle Count Reconcilation: ' . ($this->notes ?: 'Scanner Count'),
        ]);

        Flux::toast(variant: 'success', text: __('Inventory count reconciled and adjusted.'));
        $this->show_scanner_modal = false;
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
