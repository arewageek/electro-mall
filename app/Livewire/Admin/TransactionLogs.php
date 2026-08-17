<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionLogs extends Component
{
    use WithPagination;

    public $search = '';

    public $filter_type = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->with(['product', 'location', 'user'])
            ->when($this->search, function ($q) {
                $q->whereHas('product', function ($q2) {
                    $q2->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
                })
                    ->orWhereHas('user', function ($q2) {
                        $q2->where('name', 'like', '%'.$this->search.'%');
                    })
                    ->orWhere('reference', 'like', '%'.$this->search.'%')
                    ->orWhere('notes', 'like', '%'.$this->search.'%');
            })
            ->when($this->filter_type, function ($q) {
                $q->where('type', $this->filter_type);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.transaction-logs', [
            'transactions' => $transactions,
        ])->layout('layouts.app');
    }
}
