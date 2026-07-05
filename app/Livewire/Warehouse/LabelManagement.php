<?php

namespace App\Livewire\Warehouse;

use App\Models\Location;
use App\Models\Product;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Picqer\Barcode\BarcodeGeneratorPNG;

class LabelManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $type = 'product'; // 'product' or 'location'

    public $format = 'barcode'; // 'barcode' or 'qrcode'

    public $show_print_modal = false;

    public $selected_items = [];

    public $print_labels = [];

    // Generate label for a single item
    public function generateLabel($id)
    {
        $this->print_labels = [$this->buildLabelData($id)];
        $this->show_print_modal = true;
    }

    // Bulk generate labels
    public function generateBulkLabels()
    {
        if (empty($this->selected_items)) {
            Flux::toast(variant: 'warning', text: 'Select at least one item to print.');

            return;
        }

        $this->print_labels = [];
        foreach ($this->selected_items as $id) {
            $this->print_labels[] = $this->buildLabelData($id);
        }
        $this->show_print_modal = true;
    }

    private function buildLabelData($id)
    {
        if ($this->type === 'product') {
            $item = Product::findOrFail($id);
            $text = $item->barcode ?: $item->sku;
            $title = $item->name;
            $subtitle = 'SKU: '.$item->sku;
        } else {
            $item = Location::findOrFail($id);
            $text = $item->barcode;
            $title = implode(' / ', array_filter([$item->zone, $item->aisle, $item->rack, $item->shelf, $item->bin]));
            $subtitle = 'Location';
        }

        if ($this->format === 'barcode') {
            $generator = new BarcodeGeneratorPNG;
            try {
                $image = 'data:image/png;base64,'.base64_encode($generator->getBarcode($text, $generator::TYPE_CODE_128, 2, 60));
            } catch (\Exception $e) {
                $image = null;
            }
        } else {
            $options = new QROptions([
                'version' => 5,
                'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel' => QRCode::ECC_L,
            ]);
            $qrcode = new QRCode($options);
            $image = $qrcode->render($text); // This returns inline SVG
        }

        return [
            'text' => $text,
            'title' => $title,
            'subtitle' => $subtitle,
            'image' => $image,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
        $this->selected_items = [];
    }

    public function render()
    {
        if ($this->type === 'product') {
            $items = Product::when($this->search, function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%')
                    ->orWhere('barcode', 'like', '%'.$this->search.'%');
            })->latest()->paginate(20);
        } else {
            $items = Location::when($this->search, function ($q) {
                $q->where('zone', 'like', '%'.$this->search.'%')
                    ->orWhere('barcode', 'like', '%'.$this->search.'%');
            })->latest()->paginate(20);
        }

        return view('livewire.warehouse.label-management', [
            'items' => $items,
        ])->layout('layouts.app');
    }
}
