<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
    use WithPagination;

    public $start_date = '';

    public $end_date = '';

    public $filter_type = '';

    public $category_id = '';

    public $search = '';

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedCategoryId()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->start_date = '';
        $this->end_date = '';
        $this->filter_type = '';
        $this->category_id = '';
        $this->search = '';
        $this->resetPage();
    }

    public function getCategoriesProperty()
    {
        return Category::orderBy('name')->get();
    }

    public function getTransactionsQuery()
    {
        return Transaction::query()
            ->with(['product.category', 'location', 'user'])
            ->when($this->start_date, function ($q) {
                $q->whereDate('created_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($q) {
                $q->whereDate('created_at', '<=', $this->end_date);
            })
            ->when($this->filter_type, function ($q) {
                $q->where('type', $this->filter_type);
            })
            ->when($this->category_id, function ($q) {
                $q->whereHas('product', function ($q2) {
                    $q2->where('category_id', $this->category_id);
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->whereHas('product', function ($q3) {
                        $q3->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('sku', 'like', '%'.$this->search.'%');
                    })
                        ->orWhereHas('user', function ($q3) {
                            $q3->where('name', 'like', '%'.$this->search.'%');
                        })
                        ->orWhere('reference', 'like', '%'.$this->search.'%');
                });
            })
            ->latest();
    }

    public function exportCsv()
    {
        $transactions = $this->getTransactionsQuery()->get();

        $csvHeader = ['Date', 'Type', 'Category', 'Product', 'SKU', 'Location', 'Quantity', 'User', 'Reference', 'Notes'];

        $callback = function () use ($transactions, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($transactions as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    ucwords(str_replace('_', ' ', $log->type)),
                    $log->product->category->name ?? 'N/A',
                    $log->product->name,
                    $log->product->sku,
                    $log->location ? implode('/', array_filter([$log->location->zone, $log->location->aisle, $log->location->rack])) : 'Unknown',
                    $log->quantity,
                    $log->user->name ?? 'System',
                    $log->reference,
                    $log->notes,
                ]);
            }
            fclose($file);
        };

        $filename = 'report_'.date('Ymd_His').'.csv';

        return response()->streamDownload($callback, $filename, [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.reports', [
            'transactions' => $this->getTransactionsQuery()->paginate(15),
        ])->layout('layouts.app');
    }
}
