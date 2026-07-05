<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Order Picking') }}</flux:heading>
            <flux:subheading>{{ __('Pick items from warehouse locations to fulfill orders.') }}</flux:subheading>
        </div>
        <div class="flex w-full md:w-auto gap-3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search Orders...') }}" class="w-full md:w-64" />
            <flux:button variant="primary" wire:click="createOrder" icon="plus" class="shrink-0">{{ __('New Order') }}</flux:button>
        </div>
    </div>

    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Order Number') }}</flux:table.column>
                    <flux:table.column>{{ __('Customer') }}</flux:table.column>
                    <flux:table.column>{{ __('Total Amount') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($orders as $order)
                        <flux:table.row :key="$order->id">
                            <flux:table.cell>
                                <span class="font-mono font-medium">{{ $order->order_number }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $order->customer_name }}</span>
                                    @if($order->customer_email)
                                        <span class="text-xs text-zinc-500">{{ $order->customer_email }}</span>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                ${{ number_format($order->total_amount, 2) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($order->status === 'pending')
                                    <flux:badge color="zinc" size="sm">{{ __('Pending') }}</flux:badge>
                                @elseif($order->status === 'processing')
                                    <flux:badge color="blue" size="sm">{{ __('Processing') }}</flux:badge>
                                @elseif($order->status === 'picked')
                                    <flux:badge color="warning" size="sm">{{ __('Picked') }}</flux:badge>
                                @elseif($order->status === 'shipped')
                                    <flux:badge color="sky" size="sm">{{ __('Shipped') }}</flux:badge>
                                @elseif($order->status === 'delivered')
                                    <flux:badge color="success" size="sm">{{ __('Delivered') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($order->status === 'pending' || $order->status === 'processing')
                                    <flux:button size="sm" variant="subtle" wire:click="pick('{{ $order->id }}')">
                                        {{ __('Start Pick') }}
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                                {{ __('No orders found.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200">
            {{ $orders->links() }}
        </div>
    </flux:card>

    <!-- Create Order Modal -->
    <flux:modal wire:model="show_create_modal" :heading="__('Create Customer Order')" class="md:w-[800px]">
        <form wire:submit="saveOrder" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Customer Name') }}</flux:label>
                    <flux:input wire:model="customer_name" placeholder="{{ __('John Doe') }}" />
                    <flux:error name="customer_name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Customer Email') }}</flux:label>
                    <flux:input type="email" wire:model="customer_email" placeholder="{{ __('john@example.com') }}" />
                    <flux:error name="customer_email" />
                </flux:field>
            </div>

            <div class="border border-zinc-200 rounded-lg overflow-hidden">
                <div class="bg-zinc-50 px-4 py-2 border-b border-zinc-200 flex justify-between items-center">
                    <span class="font-medium">{{ __('Order Items') }}</span>
                    <flux:button size="sm" variant="subtle" icon="plus" wire:click="addOrderItem">{{ __('Add Item') }}</flux:button>
                </div>
                <div class="p-4 space-y-4">
                    @foreach($order_items as $index => $item)
                        <div class="flex flex-col md:flex-row gap-3 items-end bg-white border border-zinc-100 p-3 rounded-lg shadow-sm">
                            <flux:field class="flex-1 w-full">
                                <flux:label class="text-xs">{{ __('Product (Scan Barcode)') }}</flux:label>
                                <div class="flex gap-2 items-center">
                                    <flux:input wire:model="order_items.{{ $index }}.product_barcode" wire:keydown.enter.prevent="resolveProduct({{ $index }})" placeholder="{{ __('Scan or type barcode & hit Enter') }}" />
                                </div>
                                @if(!empty($item['product_name']))
                                    <div class="text-xs text-green-600 mt-1 font-medium flex items-center gap-1">
                                        <flux:icon.check-circle class="w-3 h-3" /> {{ $item['product_name'] }}
                                    </div>
                                @endif
                                <flux:error name="order_items.{{ $index }}.product_barcode" />
                            </flux:field>
                            
                            <flux:field class="w-full md:w-32">
                                <flux:label class="text-xs">{{ __('Quantity') }}</flux:label>
                                <flux:input type="number" min="1" wire:model="order_items.{{ $index }}.quantity" />
                                <flux:error name="order_items.{{ $index }}.quantity" />
                            </flux:field>

                            <flux:field class="w-full md:w-32">
                                <flux:label class="text-xs">{{ __('Unit Price') }}</flux:label>
                                <flux:input type="number" min="0" step="0.01" wire:model="order_items.{{ $index }}.price" />
                                <flux:error name="order_items.{{ $index }}.price" />
                            </flux:field>

                            <div class="pb-1">
                                <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500" wire:click="removeOrderItem({{ $index }})" />
                            </div>
                        </div>
                    @endforeach
                    @error('order_items') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Shipping Address') }}</flux:label>
                <flux:textarea wire:model="shipping_address" rows="2" placeholder="{{ __('123 Delivery St...') }}" />
                <flux:error name="shipping_address" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create Order') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Pick Items Modal -->
    <flux:modal wire:model="show_pick_modal" :heading="__('Pick Order Items')" class="md:w-[800px]">
        <form wire:submit="savePick" class="space-y-6">
            <div class="space-y-4">
                @foreach($pick_items as $index => $item)
                    <div class="border border-zinc-200 rounded-lg p-4 bg-zinc-50 relative">
                        <div class="font-medium mb-1">{{ $item['product_name'] }}</div>
                        <div class="text-sm text-zinc-500 mb-4">
                            {{ __('Quantity to Pick:') }} <span class="font-bold text-zinc-900 text-lg">{{ $item['quantity'] }}</span>
                        </div>
                        
                        <flux:field>
                            <flux:label>{{ __('Pick from Location (Scan Barcode)') }}</flux:label>
                            
                            <div class="flex gap-2 items-center mb-2">
                                <flux:input wire:model="pick_items.{{ $index }}.location_barcode" wire:keydown.enter.prevent="resolveLocation({{ $index }})" placeholder="{{ __('Scan location barcode...') }}" />
                            </div>
                            
                            @if(!empty($item['location_name']))
                                <div class="text-xs text-green-600 font-medium flex items-center gap-1 mb-2">
                                    <flux:icon.check-circle class="w-3 h-3" /> {{ $item['location_name'] }}
                                </div>
                            @endif

                            @if(count($item['locations']) > 0)
                                <div class="text-xs text-zinc-500">
                                    {{ __('Available at:') }}
                                    <ul class="list-disc pl-4 mt-1">
                                        @foreach($item['locations'] as $loc)
                                            <li>{{ $loc['name'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="text-red-600 text-sm py-2 px-3 bg-red-50 rounded-md border border-red-100 flex items-center gap-2 mt-2">
                                    <flux:icon.exclamation-triangle class="w-4 h-4" />
                                    {{ __('Out of Stock across all locations.') }}
                                </div>
                            @endif
                            <flux:error name="pick_items.{{ $index }}.location_barcode" />
                        </flux:field>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Confirm Pick') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
