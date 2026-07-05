<?php

namespace App\Livewire\Warehouse;

use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;
use Illuminate\Support\Str;

class LocationManagement extends Component
{
    use WithPagination;

    public $search = '';
    
    public $location_id = null;
    public $zone = '';
    public $aisle = '';
    public $rack = '';
    public $shelf = '';
    public $bin = '';
    public $barcode = '';

    public $is_editing = false;
    public $show_modal = false;

    public function rules()
    {
        return [
            'zone' => ['required', 'string', 'max:50'],
            'aisle' => ['nullable', 'string', 'max:50'],
            'rack' => ['nullable', 'string', 'max:50'],
            'shelf' => ['nullable', 'string', 'max:50'],
            'bin' => ['nullable', 'string', 'max:50'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:locations,barcode,' . $this->location_id],
        ];
    }

    public function create()
    {
        $this->reset(['location_id', 'zone', 'aisle', 'rack', 'shelf', 'bin', 'barcode']);
        $this->is_editing = false;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        
        $this->location_id = $location->id;
        $this->zone = $location->zone;
        $this->aisle = $location->aisle;
        $this->rack = $location->rack;
        $this->shelf = $location->shelf;
        $this->bin = $location->bin;
        $this->barcode = $location->barcode;
        
        $this->is_editing = true;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $barcode = $this->barcode;
        
        // Auto-generate barcode if empty
        if (empty($barcode)) {
            $parts = array_filter(['LOC', $this->zone, $this->aisle, $this->rack, $this->shelf, $this->bin]);
            $barcode = strtoupper(implode('-', $parts));
            
            // Ensure unique
            if (Location::where('barcode', $barcode)->where('id', '!=', $this->location_id)->exists()) {
                $barcode = $barcode . '-' . Str::random(4);
            }
        }

        $data = [
            'zone' => $this->zone,
            'aisle' => $this->aisle,
            'rack' => $this->rack,
            'shelf' => $this->shelf,
            'bin' => $this->bin,
            'barcode' => $barcode,
        ];

        if ($this->location_id) {
            Location::findOrFail($this->location_id)->update($data);
            Flux::toast(variant: 'success', text: __('Location updated successfully.'));
        } else {
            Location::create($data);
            Flux::toast(variant: 'success', text: __('Location created successfully.'));
        }

        $this->show_modal = false;
        $this->reset(['location_id', 'zone', 'aisle', 'rack', 'shelf', 'bin', 'barcode']);
    }
    
    public function delete($id)
    {
        Location::findOrFail($id)->delete();
        Flux::toast(variant: 'success', text: __('Location deleted.'));
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $locations = Location::query()
            ->when($this->search, function($q) {
                $q->where('zone', 'like', '%' . $this->search . '%')
                  ->orWhere('aisle', 'like', '%' . $this->search . '%')
                  ->orWhere('rack', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            })
            ->orderBy('zone')
            ->orderBy('aisle')
            ->orderBy('rack')
            ->paginate(15);

        return view('livewire.warehouse.location-management', [
            'locations' => $locations
        ])->layout('layouts.app');
    }
}
