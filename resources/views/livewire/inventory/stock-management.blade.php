<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Stock & Counts</flux:heading>
            <flux:subheading>Real-time inventory grid, adjustments, and location tracking.</flux:subheading>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search by SKU, Product, or Location..." class="w-full sm:w-72" />
            <flux:button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">Add Stock</flux:button>
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Product / SKU</flux:table.column>
            <flux:table.column>Location</flux:table.column>
            <flux:table.column>Quantity</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($stock as $item)
                <flux:table.row>
                    <flux:table.cell>
                        <div class="flex flex-col">
                            <span class="font-medium">{{ $item->product->name }}</span>
                            <span class="text-xs text-zinc-500 font-mono">{{ $item->product->sku }}</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-col">
                            <span class="font-medium text-xs">
                                Zone: {{ $item->location->zone }} | Aisle: {{ $item->location->aisle }} | Rack: {{ $item->location->rack }}
                            </span>
                            @if($item->location->barcode)
                                <span class="text-xs text-zinc-500 font-mono flex items-center gap-1 mt-1">
                                    <flux:icon.map-pin class="size-3" />
                                    {{ $item->location->barcode }}
                                </span>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="font-bold text-lg">{{ number_format($item->quantity) }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($item->quantity == 0)
                            <flux:badge color="danger" size="sm" inset="top bottom">Out of Stock</flux:badge>
                        @elseif($item->quantity < 10)
                            <flux:badge color="warning" size="sm" inset="top bottom">Low Stock</flux:badge>
                        @else
                            <flux:badge color="success" size="sm" inset="top bottom">In Stock</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                            <flux:menu>
                                <flux:menu.item wire:click="edit('{{ $item->id }}')" icon="pencil">Edit / Move</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item wire:click="delete('{{ $item->id }}')" wire:confirm="Are you sure you want to delete this stock record? This does not write to the audit log." icon="trash" variant="danger">Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">No inventory found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div>
        {{ $stock->links() }}
    </div>

    <flux:modal wire:model="show_modal" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $is_editing ? 'Update / Move Stock' : 'Add Stock Record' }}</flux:heading>
                <flux:subheading>Assign inventory to a physical warehouse location.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Product</flux:label>
                <flux:select wire:model="product_id" placeholder="Choose product..." searchable>
                    @foreach($products as $product)
                        <flux:select.option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="product_id" />
            </flux:field>

            <flux:field>
                <flux:label>Warehouse Location</flux:label>
                <flux:select wire:model="location_id" placeholder="Choose location..." searchable>
                    @foreach($locations as $location)
                        <flux:select.option value="{{ $location->id }}">Zone {{ $location->zone }}, Aisle {{ $location->aisle }}, Rack {{ $location->rack }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="location_id" />
            </flux:field>

            <flux:field>
                <flux:label>Quantity in Stock</flux:label>
                <flux:input wire:model="quantity" type="number" min="0" placeholder="0" />
                <flux:error name="quantity" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="save" variant="primary">{{ $is_editing ? 'Save Stock' : 'Add Stock' }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
