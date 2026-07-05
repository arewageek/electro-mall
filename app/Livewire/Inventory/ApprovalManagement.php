<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\InventoryVariance;
use App\Models\Transaction;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function approve($id)
    {
        $variance = InventoryVariance::findOrFail($id);
        
        if ($variance->status !== 'pending') {
            Flux::toast(variant: 'danger', text: __('Variance is already resolved.'));
            return;
        }

        $diff = $variance->counted_quantity - $variance->expected_quantity;

        // Apply inventory update
        $inv = Inventory::where('product_id', $variance->product_id)
            ->where('location_id', $variance->location_id)->first();

        if ($inv) {
            $inv->update(['quantity' => $variance->counted_quantity]);
        } else {
            Inventory::create([
                'product_id' => $variance->product_id,
                'location_id' => $variance->location_id,
                'quantity' => $variance->counted_quantity,
            ]);
        }

        Transaction::create([
            'product_id' => $variance->product_id,
            'location_id' => $variance->location_id,
            'user_id' => auth()->id(),
            'type' => 'count_adjustment',
            'quantity' => $diff,
            'notes' => 'Variance Approved: ' . $variance->notes,
        ]);

        $variance->update([
            'status' => 'approved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        Flux::toast(variant: 'success', text: __('Variance approved and inventory adjusted.'));
    }

    public function reject($id)
    {
        $variance = InventoryVariance::findOrFail($id);
        
        if ($variance->status !== 'pending') {
            Flux::toast(variant: 'danger', text: __('Variance is already resolved.'));
            return;
        }

        $variance->update([
            'status' => 'rejected',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        Flux::toast(variant: 'success', text: __('Variance rejected. Inventory remains unchanged.'));
    }

    public function render()
    {
        $variances = InventoryVariance::query()
            ->with(['product', 'location', 'counter', 'resolver'])
            ->when($this->statusFilter, function ($q) {
                if ($this->statusFilter !== 'all') {
                    $q->where('status', $this->statusFilter);
                }
            })
            ->whereHas('product', function ($q) {
                $q->when($this->search, function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.inventory.approval-management', [
            'variances' => $variances,
        ])->layout('layouts.app');
    }
}
