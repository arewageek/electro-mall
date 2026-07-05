<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Product Catalog</flux:heading>
            <flux:subheading>Manage inventory items, SKUs, and categories.</flux:subheading>
        </div>
        
        <div class="flex gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search products, SKU, or barcode..." class="w-72" />
            <flux:button wire:click="create" variant="primary" icon="plus">Add Product</flux:button>
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Product Info</flux:table.column>
            <flux:table.column>SKU / Barcode</flux:table.column>
            <flux:table.column>Supplier</flux:table.column>
            <flux:table.column>Price</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($products as $product)
                <flux:table.row>
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:avatar size="sm" :name="$product->name" />
                            <div>
                                <span class="block font-medium">{{ $product->name }}</span>
                                <span class="block text-xs text-zinc-500">{{ $product->category?->name }}</span>
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-col">
                            <span class="font-medium text-xs">{{ $product->sku }}</span>
                            @if($product->barcode)
                                <span class="text-xs text-zinc-500 font-mono flex items-center gap-1 mt-1">
                                    <flux:icon.qr-code class="size-3" />
                                    {{ $product->barcode }}
                                </span>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $product->supplier?->name ?? 'N/A' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        ${{ number_format($product->unit_price, 2) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                            <flux:menu>
                                <flux:menu.item wire:click="edit('{{ $product->id }}')" icon="pencil">Edit</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item wire:click="delete('{{ $product->id }}')" wire:confirm="Are you sure you want to delete this product?" icon="trash" variant="danger">Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">No products found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div>
        {{ $products->links() }}
    </div>

    <flux:modal wire:model="show_modal" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $is_editing ? 'Edit Product' : 'Add New Product' }}</flux:heading>
                <flux:subheading>Define the product properties and SKUs.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Product Name</flux:label>
                <flux:input wire:model="name" placeholder="e.g. Sony WH-1000XM5 Wireless Headphones" />
                <flux:error name="name" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Category</flux:label>
                    <flux:select wire:model="category_id" placeholder="Choose category...">
                        @foreach($categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Supplier</flux:label>
                    <flux:select wire:model="supplier_id" placeholder="Choose supplier...">
                        @foreach($suppliers as $supplier)
                            <flux:select.option value="{{ $supplier->id }}">{{ $supplier->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="supplier_id" />
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <flux:field>
                            <flux:label>SKU</flux:label>
                            <flux:input wire:model="sku" placeholder="PRD-1234" />
                            <flux:error name="sku" />
                        </flux:field>
                    </div>
                    <flux:button wire:click="generateSku" variant="subtle" icon="sparkles" tooltip="Generate SKU"></flux:button>
                </div>

                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <flux:field>
                            <flux:label>Barcode (Optional)</flux:label>
                            <flux:input wire:model="barcode" placeholder="Scan or enter barcode" />
                            <flux:error name="barcode" />
                        </flux:field>
                    </div>
                    <flux:button wire:click="generateBarcode" variant="subtle" icon="qr-code" tooltip="Generate Barcode"></flux:button>
                </div>
            </div>

            <flux:field>
                <flux:label>Unit Price ($)</flux:label>
                <flux:input wire:model="unit_price" type="number" step="0.01" min="0" placeholder="0.00" />
                <flux:error name="unit_price" />
            </flux:field>
            
            <flux:field>
                <flux:label>Description (Optional)</flux:label>
                <flux:textarea wire:model="description" rows="3" placeholder="Brief description of the product..." />
                <flux:error name="description" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="save" variant="primary">{{ $is_editing ? 'Save Changes' : 'Create Product' }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
