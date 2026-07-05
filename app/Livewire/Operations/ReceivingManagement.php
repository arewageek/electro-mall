<?php

namespace App\Livewire\Operations;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Location;
use App\Models\Inventory;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

class ReceivingManagement extends Component
{
    use WithPagination;

    public $search = '';
    
    // Create PO Modal State
    public $show_create_modal = false;
    public $supplier_id = '';
    public $expected_delivery_date = '';
    public $notes = '';
    public $po_items = []; // [['product_id' => '', 'quantity' => 1, 'price' => 0]]

    // Receive Modal State
    public $show_receive_modal = false;
    public $receiving_po_id = null;
    public $receive_items = []; 
    // [['item_id' => id, 'product_name' => name, 'ordered' => X, 'received_so_far' => Y, 'receiving_now' => 0, 'location_id' => '']]

    public function mount()
    {
        $this->addPoItem();
    }

    public function addPoItem()
    {
        $this->po_items[] = ['product_id' => '', 'quantity' => 1, 'price' => 0];
    }

    public function removePoItem($index)
    {
        unset($this->po_items[$index]);
        $this->po_items = array_values($this->po_items);
    }

    public function createPo()
    {
        $this->reset(['supplier_id', 'expected_delivery_date', 'notes']);
        $this->po_items = [];
        $this->addPoItem();
        $this->show_create_modal = true;
        $this->resetValidation();
    }

    public function savePo()
    {
        $this->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'expected_delivery_date' => ['nullable', 'date'],
            'po_items' => ['required', 'array', 'min:1'],
            'po_items.*.product_id' => ['required', 'exists:products,id'],
            'po_items.*.quantity' => ['required', 'numeric', 'min:1'],
            'po_items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        $po = PurchaseOrder::create([
            'supplier_id' => $this->supplier_id,
            'user_id' => auth()->id(),
            'po_number' => 'PO-' . date('Ymd') . '-' . rand(1000, 9999),
            'status' => 'submitted',
            'expected_delivery_date' => $this->expected_delivery_date ?: null,
            'notes' => $this->notes,
        ]);

        foreach ($this->po_items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $item['product_id'],
                'quantity_ordered' => $item['quantity'],
                'quantity_received' => 0,
                'unit_price' => $item['price'],
            ]);
        }

        Flux::toast(variant: 'success', text: __('Purchase order created successfully.'));
        $this->show_create_modal = false;
    }

    public function receive($id)
    {
        $po = PurchaseOrder::with(['items.product'])->findOrFail($id);
        
        $this->receiving_po_id = $po->id;
        $this->receive_items = [];
        
        foreach ($po->items as $item) {
            if ($item->quantity_received < $item->quantity_ordered) {
                $this->receive_items[] = [
                    'item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name . ' (' . $item->product->sku . ')',
                    'ordered' => $item->quantity_ordered,
                    'received_so_far' => $item->quantity_received,
                    'receiving_now' => 0,
                    'location_id' => '',
                ];
            }
        }

        if (empty($this->receive_items)) {
            Flux::toast(variant: 'warning', text: __('This purchase order is already fully received.'));
            return;
        }

        $this->show_receive_modal = true;
    }

    public function saveReceive()
    {
        $this->validate([
            'receive_items.*.receiving_now' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($this->receive_items as $index => $rItem) {
            if ($rItem['receiving_now'] > 0 && empty($rItem['location_id'])) {
                $this->addError("receive_items.{$index}.location_id", __('You must select a location when receiving an item.'));
                return;
            }
        }

        $po = PurchaseOrder::findOrFail($this->receiving_po_id);
        $total_received_in_this_batch = 0;

        foreach ($this->receive_items as $rItem) {
            if ($rItem['receiving_now'] > 0) {
                $total_received_in_this_batch += $rItem['receiving_now'];
                
                $poItem = PurchaseOrderItem::findOrFail($rItem['item_id']);
                $poItem->increment('quantity_received', $rItem['receiving_now']);

                // Add to inventory
                $inventory = Inventory::firstOrCreate(
                    [
                        'product_id' => $rItem['product_id'],
                        'location_id' => $rItem['location_id']
                    ],
                    ['quantity' => 0]
                );
                
                $inventory->increment('quantity', $rItem['receiving_now']);

                // Log transaction
                Transaction::create([
                    'product_id' => $rItem['product_id'],
                    'location_id' => $rItem['location_id'],
                    'user_id' => auth()->id(),
                    'type' => 'receive',
                    'quantity' => $rItem['receiving_now'],
                    'reference' => $po->po_number,
                    'notes' => 'Received from PO ' . $po->po_number,
                ]);
            }
        }

        if ($total_received_in_this_batch > 0) {
            Flux::toast(variant: 'success', text: __('Items received and added to inventory.'));
            
            // Check if PO is fully received
            $all_received = true;
            foreach ($po->items as $item) {
                if ($item->quantity_received < $item->quantity_ordered) {
                    $all_received = false;
                    break;
                }
            }
            
            $po->update([
                'status' => $all_received ? 'received' : 'partially_received'
            ]);
        } else {
            Flux::toast(variant: 'warning', text: __('No items were received.'));
        }

        $this->show_receive_modal = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $purchase_orders = PurchaseOrder::query()
            ->with(['supplier', 'items'])
            ->when($this->search, function($q) {
                $q->where('po_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('supplier', function($q2) {
                      $q2->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.operations.receiving-management', [
            'purchase_orders' => $purchase_orders,
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'locations' => Location::orderBy('zone')->orderBy('aisle')->orderBy('rack')->get(),
        ])->layout('layouts.app');
    }
}
