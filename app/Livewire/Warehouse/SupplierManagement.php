<?php

namespace App\Livewire\Warehouse;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

class SupplierManagement extends Component
{
    use WithPagination;

    public $search = '';
    
    public $supplier_id = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $contact_person = '';
    public $address = '';

    public $is_editing = false;
    public $show_modal = false;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ];
    }

    public function create()
    {
        $this->reset(['supplier_id', 'name', 'email', 'phone', 'contact_person', 'address']);
        $this->is_editing = false;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $this->supplier_id = $supplier->id;
        $this->name = $supplier->name;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->contact_person = $supplier->contact_person;
        $this->address = $supplier->address;
        
        $this->is_editing = true;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'contact_person' => $this->contact_person,
            'address' => $this->address,
        ];

        if ($this->supplier_id) {
            Supplier::findOrFail($this->supplier_id)->update($data);
            Flux::toast(variant: 'success', text: __('Supplier updated successfully.'));
        } else {
            Supplier::create($data);
            Flux::toast(variant: 'success', text: __('Supplier created successfully.'));
        }

        $this->show_modal = false;
        $this->reset(['supplier_id', 'name', 'email', 'phone', 'contact_person', 'address']);
    }
    
    public function delete($id)
    {
        Supplier::findOrFail($id)->delete();
        Flux::toast(variant: 'success', text: __('Supplier deleted successfully.'));
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $suppliers = Supplier::query()
            ->when($this->search, function($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('contact_person', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.warehouse.supplier-management', [
            'suppliers' => $suppliers,
        ])->layout('layouts.app');
    }
}
