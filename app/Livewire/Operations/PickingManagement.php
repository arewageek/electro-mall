<?php

namespace App\Livewire\Operations;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class PickingManagement extends Component
{
    use WithPagination;

    public $search = '';

    // Create Order Modal State
    public $show_create_modal = false;

    public $customer_name = '';

    public $customer_email = '';

    public $shipping_address = '';

    public $order_items = []; // [['product_id' => '', 'product_barcode' => '', 'product_name' => '', 'quantity' => 1, 'price' => 0]]

    // Pick Order Modal State
    public $show_pick_modal = false;

    public $picking_order_id = null;

    public $pick_items = [];
    // [['item_id' => id, 'product_id' => id, 'product_name' => name, 'quantity' => X, 'locations' => [...], 'selected_location_id' => '']]

    public function mount()
    {
        $this->addOrderItem();
    }

    public function addOrderItem()
    {
        $this->order_items[] = ['product_id' => '', 'product_barcode' => '', 'product_name' => '', 'quantity' => 1, 'price' => 0];
    }

    public function resolveProduct($index)
    {
        $barcode = $this->order_items[$index]['product_barcode'] ?? '';
        if (empty($barcode)) {
            return;
        }

        $product = Product::where('barcode', $barcode)->orWhere('sku', $barcode)->first();
        if ($product) {
            $this->order_items[$index]['product_id'] = $product->id;
            $this->order_items[$index]['product_name'] = $product->name.' ('.$product->sku.')';
            $this->resetErrorBag("order_items.{$index}.product_barcode");
        } else {
            $this->order_items[$index]['product_id'] = '';
            $this->order_items[$index]['product_name'] = '';
            $this->addError("order_items.{$index}.product_barcode", __('Invalid product barcode.'));
        }
    }

    public function removeOrderItem($index)
    {
        unset($this->order_items[$index]);
        $this->order_items = array_values($this->order_items);
    }

    public function createOrder()
    {
        $this->reset(['customer_name', 'customer_email', 'shipping_address']);
        $this->order_items = [];
        $this->addOrderItem();
        $this->show_create_modal = true;
        $this->resetValidation();
    }

    public function saveOrder()
    {
        $this->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email'],
            'shipping_address' => ['nullable', 'string'],
            'order_items' => ['required', 'array', 'min:1'],
            'order_items.*.product_id' => ['required', 'exists:products,id'],
            'order_items.*.quantity' => ['required', 'numeric', 'min:1'],
            'order_items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        $total_amount = collect($this->order_items)->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });

        $order = Order::create([
            'order_number' => 'ORD-'.date('Ymd').'-'.rand(1000, 9999),
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'shipping_address' => $this->shipping_address,
            'status' => 'pending',
            'total_amount' => $total_amount,
        ]);

        foreach ($this->order_items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
            ]);
        }

        Flux::toast(variant: 'success', text: __('Order created successfully.'));
        $this->show_create_modal = false;
    }

    public function pick($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);

        $this->picking_order_id = $order->id;
        $this->pick_items = [];

        foreach ($order->items as $item) {
            // Find where this product is located in the warehouse
            $inventory_locations = Inventory::with('location')
                ->where('product_id', $item->product_id)
                ->where('quantity', '>', 0)
                ->get()
                ->map(function ($inv) {
                    return [
                        'id' => $inv->location_id,
                        'name' => implode(' / ', array_filter([$inv->location->zone, $inv->location->aisle, $inv->location->rack, $inv->location->shelf, $inv->location->bin]))." (Qty: {$inv->quantity})",
                        'available' => $inv->quantity,
                    ];
                })->toArray();

            $this->pick_items[] = [
                'item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name.' ('.$item->product->sku.')',
                'quantity' => $item->quantity,
                'locations' => $inventory_locations,
                'selected_location_id' => '',
                'location_barcode' => '',
                'location_name' => '',
            ];
        }

        $this->show_pick_modal = true;
    }

    public function resolveLocation($index)
    {
        $barcode = $this->pick_items[$index]['location_barcode'] ?? '';
        if (empty($barcode)) {
            return;
        }

        $location = Location::where('barcode', $barcode)->first();
        if ($location) {
            $has_inventory = collect($this->pick_items[$index]['locations'])->contains('id', $location->id);

            if ($has_inventory) {
                $this->pick_items[$index]['selected_location_id'] = $location->id;
                $this->pick_items[$index]['location_name'] = implode(' / ', array_filter([$location->zone, $location->aisle, $location->rack, $location->shelf, $location->bin]));
                $this->resetErrorBag("pick_items.{$index}.location_barcode");
            } else {
                $this->pick_items[$index]['selected_location_id'] = '';
                $this->pick_items[$index]['location_name'] = '';
                $this->addError("pick_items.{$index}.location_barcode", __('This location does not have stock for this item.'));
            }
        } else {
            $this->pick_items[$index]['selected_location_id'] = '';
            $this->pick_items[$index]['location_name'] = '';
            $this->addError("pick_items.{$index}.location_barcode", __('Invalid location barcode.'));
        }
    }

    public function savePick()
    {
        foreach ($this->pick_items as $index => $pItem) {
            if (empty($pItem['selected_location_id'])) {
                $this->addError("pick_items.{$index}.location_barcode", __('You must scan or enter a valid location barcode.'));

                return;
            }
        }

        $order = Order::findOrFail($this->picking_order_id);

        // Validate sufficient stock first
        foreach ($this->pick_items as $pItem) {
            $inventory = Inventory::where('product_id', $pItem['product_id'])
                ->where('location_id', $pItem['selected_location_id'])
                ->first();

            if (! $inventory || $inventory->quantity < $pItem['quantity']) {
                Flux::toast(variant: 'danger', text: "Insufficient stock for {$pItem['product_name']} at selected location.");

                return;
            }
        }

        // Apply deductions
        foreach ($this->pick_items as $pItem) {
            $inventory = Inventory::where('product_id', $pItem['product_id'])
                ->where('location_id', $pItem['selected_location_id'])
                ->first();

            $inventory->decrement('quantity', $pItem['quantity']);

            // Log transaction
            Transaction::create([
                'product_id' => $pItem['product_id'],
                'location_id' => $pItem['selected_location_id'],
                'user_id' => auth()->id(),
                'type' => 'pick',
                'quantity' => -$pItem['quantity'], // Negative for picking
                'reference' => $order->order_number,
                'notes' => 'Picked for Order '.$order->order_number,
            ]);
        }

        $order->update([
            'status' => 'picked',
        ]);

        Flux::toast(variant: 'success', text: __('Order successfully picked from inventory.'));
        $this->show_pick_modal = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::query()
            ->with(['items'])
            ->when($this->search, function ($q) {
                $q->where('order_number', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_name', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.operations.picking-management', [
            'orders' => $orders,
        ])->layout('layouts.app');
    }
}
