<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Receiving & Purchase Orders') }}</flux:heading>
            <flux:subheading>{{ __('Manage incoming stock and receive purchase orders.') }}</flux:subheading>
        </div>
        <div class="flex w-full md:w-auto gap-3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search POs...') }}" class="w-full md:w-64" />
            <flux:button variant="primary" wire:click="createPo" icon="plus" class="shrink-0">{{ __('Create PO') }}</flux:button>
        </div>
    </div>

    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('PO Number') }}</flux:table.column>
                    <flux:table.column>{{ __('Supplier') }}</flux:table.column>
                    <flux:table.column>{{ __('Date Expected') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Progress') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($purchase_orders as $po)
                        @php
                            $total_ordered = $po->items->sum('quantity_ordered');
                            $total_received = $po->items->sum('quantity_received');
                            $progress = $total_ordered > 0 ? round(($total_received / $total_ordered) * 100) : 0;
                        @endphp
                        <flux:table.row :key="$po->id">
                            <flux:table.cell>
                                <span class="font-mono font-medium">{{ $po->po_number }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="font-medium">{{ $po->supplier->name }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-500">
                                {{ $po->expected_delivery_date ? \Carbon\Carbon::parse($po->expected_delivery_date)->format('M d, Y') : '-' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($po->status === 'draft' || $po->status === 'submitted')
                                    <flux:badge color="zinc" size="sm">{{ __('Pending') }}</flux:badge>
                                @elseif($po->status === 'partially_received')
                                    <flux:badge color="warning" size="sm">{{ __('Partial') }}</flux:badge>
                                @elseif($po->status === 'received')
                                    <flux:badge color="success" size="sm">{{ __('Received') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-zinc-200 rounded-full h-2 mt-1">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-zinc-500">{{ $progress }}%</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($po->status !== 'received')
                                    <flux:button size="sm" variant="subtle" wire:click="receive('{{ $po->id }}')">
                                        {{ __('Receive') }}
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                                {{ __('No purchase orders found.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200">
            {{ $purchase_orders->links() }}
        </div>
    </flux:card>

    <!-- Create PO Modal -->
    <flux:modal wire:model="show_create_modal" :heading="__('Create Purchase Order')" class="md:w-[800px]">
        <form wire:submit="savePo" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Supplier') }}</flux:label>
                    <flux:select wire:model="supplier_id" placeholder="{{ __('Select a supplier...') }}">
                        @foreach($suppliers as $supplier)
                            <flux:select.option value="{{ $supplier->id }}">{{ $supplier->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="supplier_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Expected Delivery Date') }}</flux:label>
                    <flux:input type="date" wire:model="expected_delivery_date" />
                    <flux:error name="expected_delivery_date" />
                </flux:field>
            </div>

            <div class="border border-zinc-200 rounded-lg overflow-hidden">
                <div class="bg-zinc-50 px-4 py-2 border-b border-zinc-200 flex justify-between items-center">
                    <span class="font-medium">{{ __('Order Items') }}</span>
                    <flux:button size="sm" variant="subtle" icon="plus" wire:click="addPoItem">{{ __('Add Item') }}</flux:button>
                </div>
                <div class="p-4 space-y-4">
                    @foreach($po_items as $index => $item)
                        <div class="flex flex-col md:flex-row gap-3 items-end bg-white border border-zinc-100 p-3 rounded-lg shadow-sm">
                            <flux:field class="flex-1 w-full">
                                <flux:label class="text-xs">{{ __('Product (Scan Barcode)') }}</flux:label>
                                <div class="flex gap-2 items-center">
                                    <flux:input wire:model="po_items.{{ $index }}.product_barcode" wire:keydown.enter.prevent="resolveProduct({{ $index }})" placeholder="{{ __('Scan or type barcode & hit Enter') }}" />
                                    <x-barcode-scanner id="scanner-po-product-{{ $index }}" x-on:scan="$wire.set('po_items.{{ $index }}.product_barcode', $event.detail.code); $wire.resolveProduct({{ $index }})" />
                                </div>
                                @if(!empty($item['product_name']))
                                    <div class="text-xs text-green-600 mt-1 font-medium flex items-center gap-1">
                                        <flux:icon.check-circle class="w-3 h-3" /> {{ $item['product_name'] }}
                                    </div>
                                @endif
                                <flux:error name="po_items.{{ $index }}.product_barcode" />
                                <flux:error name="po_items.{{ $index }}.product_id" />
                            </flux:field>
                            
                            <flux:field class="w-full md:w-32">
                                <flux:label class="text-xs">{{ __('Quantity') }}</flux:label>
                                <flux:input type="number" min="1" wire:model="po_items.{{ $index }}.quantity" />
                                <flux:error name="po_items.{{ $index }}.quantity" />
                            </flux:field>

                            <flux:field class="w-full md:w-32">
                                <flux:label class="text-xs">{{ __('Unit Price') }}</flux:label>
                                <flux:input type="number" min="0" step="0.01" wire:model="po_items.{{ $index }}.price" />
                                <flux:error name="po_items.{{ $index }}.price" />
                            </flux:field>

                            <div class="pb-1">
                                <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500" wire:click="removePoItem({{ $index }})" />
                            </div>
                        </div>
                    @endforeach
                    @error('po_items') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Notes') }}</flux:label>
                <flux:textarea wire:model="notes" rows="2" placeholder="{{ __('Optional notes...') }}" />
                <flux:error name="notes" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create PO') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Receive Items Modal -->
    <flux:modal wire:model="show_receive_modal" :heading="__('Receive Items')" class="md:w-[800px]">
        <form wire:submit="saveReceive" class="space-y-6">
            <div class="space-y-4">
                @foreach($receive_items as $index => $item)
                    <div class="border border-zinc-200 rounded-lg p-4 bg-zinc-50 relative">
                        <div class="font-medium mb-1">{{ $item['product_name'] }}</div>
                        <div class="text-sm text-zinc-500 mb-4">
                            {{ __('Ordered:') }} <span class="font-semibold text-zinc-900">{{ $item['ordered'] }}</span> | 
                            {{ __('Received so far:') }} <span class="font-semibold text-zinc-900">{{ $item['received_so_far'] }}</span>
                        </div>
                        
                        <div class="flex gap-4">
                            <flux:field class="w-32">
                                <flux:label>{{ __('Receiving Now') }}</flux:label>
                                <flux:input type="number" min="0" max="{{ $item['ordered'] - $item['received_so_far'] }}" wire:model="receive_items.{{ $index }}.receiving_now" />
                                <flux:error name="receive_items.{{ $index }}.receiving_now" />
                            </flux:field>

                            <flux:field class="flex-1">
                                <flux:label>{{ __('Put-away Location (Scan Barcode)') }}</flux:label>
                                <div class="flex gap-2 items-center">
                                    <flux:input wire:model="receive_items.{{ $index }}.location_barcode" wire:keydown.enter.prevent="resolveLocation({{ $index }})" placeholder="{{ __('Scan location barcode...') }}" />
                                    <x-barcode-scanner id="scanner-receive-location-{{ $index }}" x-on:scan="$wire.set('receive_items.{{ $index }}.location_barcode', $event.detail.code); $wire.resolveLocation({{ $index }})" />
                                </div>
                                @if(!empty($item['location_name']))
                                    <div class="text-xs text-green-600 mt-1 font-medium flex items-center gap-1">
                                        <flux:icon.check-circle class="w-3 h-3" /> {{ $item['location_name'] }}
                                    </div>
                                @endif
                                <flux:error name="receive_items.{{ $index }}.location_barcode" />
                            </flux:field>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Confirm Receipt') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
