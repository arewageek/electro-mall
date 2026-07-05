<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Flux\Flux;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    
    public $product_id = null;
    public $category_id = '';
    public $supplier_id = '';
    public $name = '';
    public $sku = '';
    public $barcode = '';
    public $description = '';
    public $unit_price = '';

    public $is_editing = false;
    public $show_modal = false;
    
    public $show_print_modal = false;
    public $print_product = null;
    public $print_barcode_svg = '';
    public $print_qrcode_svg = '';

    public function rules()
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $this->product_id],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:products,barcode,' . $this->product_id],
            'description' => ['nullable', 'string'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function printLabel($id)
    {
        $product = Product::findOrFail($id);
        
        if (empty($product->barcode)) {
            Flux::toast(variant: 'warning', text: __('This product does not have a barcode. Please edit and generate one first.'));
            return;
        }
        
        $this->print_product = $product;
        
        // Generate 1D Barcode (CODE128)
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $this->print_barcode_svg = $generator->getBarcode($product->barcode, $generator::TYPE_CODE_128, 2, 60);

        // Generate QR Code
        $options = new \chillerlan\QRCode\QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'eccLevel'   => \chillerlan\QRCode\Common\EccLevel::L,
            'addQuietzone' => false,
        ]);
        $this->print_qrcode_svg = (new \chillerlan\QRCode\QRCode($options))->render($product->barcode);

        $this->show_print_modal = true;
    }

    public function create()
    {
        $this->reset(['product_id', 'category_id', 'supplier_id', 'name', 'sku', 'barcode', 'description', 'unit_price']);
        $this->is_editing = false;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        $this->product_id = $product->id;
        $this->category_id = $product->category_id;
        $this->supplier_id = $product->supplier_id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->barcode = $product->barcode;
        $this->description = $product->description;
        $this->unit_price = $product->unit_price;
        
        $this->is_editing = true;
        $this->show_modal = true;
        $this->resetValidation();
    }

    public function generateSku()
    {
        if (empty($this->name)) {
            Flux::toast(variant: 'danger', text: __('Enter a product name first to generate SKU.'));
            return;
        }
        
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->name), 0, 3));
        $this->sku = $prefix . '-' . strtoupper(Str::random(6));
    }

    public function generateBarcode()
    {
        // Simple EAN-13 style random barcode generator for demonstration
        $this->barcode = '84' . rand(10000000000, 99999999999);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'unit_price' => $this->unit_price,
        ];

        if ($this->product_id) {
            Product::findOrFail($this->product_id)->update($data);
            Flux::toast(variant: 'success', text: __('Product updated successfully.'));
        } else {
            Product::create($data);
            Flux::toast(variant: 'success', text: __('Product created successfully.'));
        }

        $this->show_modal = false;
        $this->reset(['product_id', 'category_id', 'supplier_id', 'name', 'sku', 'barcode', 'description', 'unit_price']);
    }
    
    public function delete($id)
    {
        Product::findOrFail($id)->delete();
        Flux::toast(variant: 'success', text: __('Product deleted successfully.'));
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, function($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['category', 'supplier'])
            ->latest()
            ->paginate(10);

        return view('livewire.inventory.product-catalog', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
