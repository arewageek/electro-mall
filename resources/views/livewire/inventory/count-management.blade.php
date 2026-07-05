<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Inventory Counts') }}</flux:heading>
            <flux:subheading>{{ __('Reconcile physical inventory against expected system quantities.') }}</flux:subheading>
        </div>
        <div class="w-full md:w-auto flex gap-3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search inventory...') }}" class="w-full md:w-64" />
            <flux:button variant="primary" wire:click="openScanner" icon="qr-code" class="shrink-0">{{ __('Scanner Count') }}</flux:button>
        </div>
    </div>

    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Product') }}</flux:table.column>
                    <flux:table.column>{{ __('SKU') }}</flux:table.column>
                    <flux:table.column>{{ __('Location') }}</flux:table.column>
                    <flux:table.column>{{ __('Expected Qty') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($stock as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-zinc-100 flex items-center justify-center text-zinc-500">
                                        <flux:icon.cube class="w-5 h-5" />
                                    </div>
                                    <span class="font-medium">{{ $item->product->name }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-500">{{ $item->product->sku }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="zinc" size="sm">
                                    {{ $item->location->zone }}-{{ $item->location->aisle }}-{{ $item->location->rack }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="font-medium {{ $item->quantity <= 150 ? 'text-red-600' : 'text-zinc-900' }}">
                                    {{ number_format($item->quantity) }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="sm" variant="subtle" wire:click="edit('{{ $item->id }}')">
                                    {{ __('Count') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                                {{ __('No inventory records found.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200">
            {{ $stock->links() }}
        </div>
    </flux:card>

    <flux:modal wire:model="show_modal" :heading="__('Reconcile Count')" class="max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div class="bg-zinc-50 rounded-lg p-4 space-y-2 border border-zinc-200">
                <div class="flex justify-between">
                    <span class="text-zinc-500 text-sm">{{ __('Product:') }}</span>
                    <span class="font-medium text-sm">{{ $product_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 text-sm">{{ __('Location:') }}</span>
                    <span class="font-medium text-sm">{{ $location_name }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-zinc-200 mt-2">
                    <span class="font-medium">{{ __('Expected System Quantity:') }}</span>
                    <flux:badge color="zinc">{{ number_format($expected_quantity) }}</flux:badge>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Physical Count (Actual)') }}</flux:label>
                <flux:input wire:model="counted_quantity" type="number" min="0" placeholder="0" />
                <flux:error name="counted_quantity" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Notes / Reason for Variance') }}</flux:label>
                <flux:textarea wire:model="notes" rows="3" placeholder="{{ __('Found extra items behind rack...') }}" />
                <flux:error name="notes" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit Count') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Scanner Count Modal -->
    <flux:modal wire:model="show_scanner_modal" :heading="__('Scanner Cycle Count')" class="md:w-[600px]">
        <form wire:submit="saveScannerCount" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('1. Scan Location') }}</flux:label>
                    <flux:input wire:model="scan_location_barcode" wire:keydown.enter.prevent="resolveScanLocation" placeholder="{{ __('Location Barcode...') }}" autofocus />
                    @if($scanned_location_name)
                        <div class="text-xs text-green-600 font-medium flex items-center gap-1 mt-1">
                            <flux:icon.check-circle class="w-3 h-3" /> {{ $scanned_location_name }}
                        </div>
                    @endif
                    <flux:error name="scan_location_barcode" />
                    <flux:error name="scanned_location_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('2. Scan Product') }}</flux:label>
                    <flux:input wire:model="scan_product_barcode" wire:keydown.enter.prevent="resolveScanProduct" placeholder="{{ __('Product Barcode...') }}" />
                    @if($scanned_product_name)
                        <div class="text-xs text-green-600 font-medium flex items-center gap-1 mt-1">
                            <flux:icon.check-circle class="w-3 h-3" /> {{ $scanned_product_name }}
                        </div>
                    @endif
                    <flux:error name="scan_product_barcode" />
                    <flux:error name="scanned_product_id" />
                </flux:field>
            </div>

            @if($scanned_location_id && $scanned_product_id)
                <div class="bg-zinc-50 rounded-lg p-4 flex justify-between items-center border border-zinc-200">
                    <span class="font-medium text-zinc-700">{{ __('Expected System Quantity:') }}</span>
                    <flux:badge size="lg" color="zinc">{{ number_format($scanned_expected_quantity) }}</flux:badge>
                </div>

                <flux:field>
                    <flux:label>{{ __('3. Enter Physical Count') }}</flux:label>
                    <flux:input wire:model="counted_quantity" type="number" min="0" placeholder="0" class="text-lg font-bold" />
                    <flux:error name="counted_quantity" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Notes (Optional)') }}</flux:label>
                    <flux:textarea wire:model="notes" rows="2" placeholder="{{ __('Reason for discrepancy...') }}" />
                    <flux:error name="notes" />
                </flux:field>
            @else
                <div class="p-6 text-center text-zinc-500 border border-dashed border-zinc-300 rounded-lg bg-zinc-50/50">
                    <flux:icon.qr-code class="w-8 h-8 mx-auto mb-2 opacity-50" />
                    <p>{{ __('Please scan both a location and a product to begin counting.') }}</p>
                </div>
            @endif

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" :disabled="!$scanned_location_id || !$scanned_product_id">{{ __('Submit Count') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
