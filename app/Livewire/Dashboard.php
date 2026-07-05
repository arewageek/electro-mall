<?php

namespace App\Livewire;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Transaction;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // Summary Metrics
        $total_items_in_stock = Inventory::sum('quantity');
        
        $pending_pos_count = PurchaseOrder::whereIn('status', ['draft', 'submitted', 'partially_received'])->count();
        $pending_pos = PurchaseOrder::whereIn('status', ['draft', 'submitted', 'partially_received'])
            ->latest()->take(3)->get();

        $active_picks_count = Order::whereIn('status', ['pending', 'processing'])->count();
        $active_picks = Order::whereIn('status', ['pending', 'processing'])
            ->latest()->take(3)->get();

        // Low stock alerts
        // This requires joining products and their total inventory, or calculating it.
        // A simpler way for a prototype is to query products and sum relationships.
        $products = Product::withSum('inventories as total_qty', 'quantity')->get();
        $low_stock_products = $products->filter(function($product) {
            // Since min_stock_level is not in the schema, we use a default of 150 for alerts to show more items
            return $product->total_qty <= 150;
        })->sortBy('total_qty')->take(5);

        // Recent activity
        $recent_transactions = Transaction::with(['product', 'user', 'location'])
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.dashboard', [
            'total_items_in_stock' => $total_items_in_stock,
            'pending_pos_count' => $pending_pos_count,
            'pending_pos' => $pending_pos,
            'active_picks_count' => $active_picks_count,
            'active_picks' => $active_picks,
            'low_stock_products' => $low_stock_products,
            'recent_transactions' => $recent_transactions,
        ])->layout('layouts.app', ['title' => __('Dashboard')]);
    }
}
