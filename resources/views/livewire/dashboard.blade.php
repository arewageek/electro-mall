<div>
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Warehouse Overview') }}</flux:heading>
        <flux:subheading>{{ __('Real-time metrics and pending operations.') }}</flux:subheading>
    </div>

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                    <flux:icon.cube class="size-6" />
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-500">{{ __('Total Items in Stock') }}</div>
                    <div class="text-2xl font-bold text-zinc-900">{{ number_format($total_items_in_stock) }}</div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-100 text-orange-600 rounded-lg">
                    <flux:icon.arrow-up-tray class="size-6" />
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-500">{{ __('Active Pick Orders') }}</div>
                    <div class="text-2xl font-bold text-zinc-900">{{ $active_picks_count }}</div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-100 text-emerald-600 rounded-lg">
                    <flux:icon.arrow-down-tray class="size-6" />
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-500">{{ __('Pending Purchase Orders') }}</div>
                    <div class="text-2xl font-bold text-zinc-900">{{ $pending_pos_count }}</div>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left Column -->
        <div class="space-y-6">
            <!-- Active Picks -->
            @can('order.pick')
            <flux:card>
                <div class="flex justify-between items-center mb-4">
                    <flux:heading size="lg">{{ __('Orders Awaiting Picking') }}</flux:heading>
                    <flux:button size="sm" variant="subtle" :href="route('operations.picking')" wire:navigate>{{ __('View All') }}</flux:button>
                </div>
                
                <div class="space-y-3">
                    @forelse($active_picks as $order)
                        <div class="flex items-center justify-between p-3 bg-zinc-50 rounded-lg border border-zinc-100">
                            <div>
                                <div class="font-medium font-mono text-sm">{{ $order->order_number }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->customer_name }}</div>
                            </div>
                            <flux:button size="sm" variant="ghost" :href="route('operations.picking')" wire:navigate>{{ __('Pick') }}</flux:button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-zinc-500 text-sm">{{ __('No pending orders to pick.') }}</div>
                    @endforelse
                </div>
            </flux:card>
            @endcan

            <!-- Pending POs -->
            @can('shipment.receive')
            <flux:card>
                <div class="flex justify-between items-center mb-4">
                    <flux:heading size="lg">{{ __('Inbound Purchase Orders') }}</flux:heading>
                    <flux:button size="sm" variant="subtle" :href="route('operations.receiving')" wire:navigate>{{ __('View All') }}</flux:button>
                </div>
                
                <div class="space-y-3">
                    @forelse($pending_pos as $po)
                        <div class="flex items-center justify-between p-3 bg-zinc-50 rounded-lg border border-zinc-100">
                            <div>
                                <div class="font-medium font-mono text-sm">{{ $po->po_number }}</div>
                                <div class="text-xs text-zinc-500">
                                    {{ __('Expected:') }} {{ $po->expected_delivery_date ? \Carbon\Carbon::parse($po->expected_delivery_date)->format('M d') : 'N/A' }}
                                </div>
                            </div>
                            <flux:button size="sm" variant="ghost" :href="route('operations.receiving')" wire:navigate>{{ __('Receive') }}</flux:button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-zinc-500 text-sm">{{ __('No inbound purchase orders.') }}</div>
                    @endforelse
                </div>
            </flux:card>
            @endcan
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            
            <!-- Low Stock Alerts -->
            <flux:card>
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon.exclamation-triangle class="size-5 text-red-500" />
                    <flux:heading size="lg" class="text-red-600">{{ __('Low Stock Alerts') }}</flux:heading>
                </div>
                
                <div class="space-y-3">
                    @forelse($low_stock_products as $product)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                            <div>
                                <div class="font-medium text-sm">{{ $product->name }}</div>
                                <div class="text-xs text-red-500 font-mono">{{ $product->sku }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-red-700">{{ $product->total_qty ?? 0 }}</div>
                                <div class="text-xs text-red-500">{{ __('Min:') }} 150</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-zinc-500 text-sm">{{ __('All products are above minimum stock levels.') }}</div>
                    @endforelse
                </div>
            </flux:card>

            <!-- Recent Activity Log -->
            <flux:card>
                <div class="flex justify-between items-center mb-4">
                    <flux:heading size="lg">{{ __('Recent Activity') }}</flux:heading>
                    <flux:button size="sm" variant="subtle" :href="route('admin.logs')" wire:navigate>{{ __('Logs') }}</flux:button>
                </div>

                <div class="space-y-4">
                    @forelse($recent_transactions as $log)
                        <div class="flex gap-3">
                            <div class="mt-1">
                                @if($log->type === 'receive')
                                    <div class="p-1.5 bg-green-100 text-green-600 rounded-full"><flux:icon.arrow-down-tray class="size-3" /></div>
                                @elseif($log->type === 'pick')
                                    <div class="p-1.5 bg-orange-100 text-orange-600 rounded-full"><flux:icon.arrow-up-tray class="size-3" /></div>
                                @else
                                    <div class="p-1.5 bg-zinc-100 text-zinc-600 rounded-full"><flux:icon.arrows-right-left class="size-3" /></div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="text-sm">
                                    <span class="font-medium text-zinc-900">{{ $log->user->name }}</span>
                                    <span class="text-zinc-500">
                                        @if($log->type === 'receive')
                                            {{ __('received') }} <span class="font-medium text-zinc-700">{{ abs($log->quantity) }}</span> {{ __('units of') }}
                                        @elseif($log->type === 'pick')
                                            {{ __('picked') }} <span class="font-medium text-zinc-700">{{ abs($log->quantity) }}</span> {{ __('units of') }}
                                        @elseif($log->type === 'count')
                                            {{ __('counted') }} <span class="font-medium text-zinc-700">{{ abs($log->quantity) }}</span> {{ __('units of') }}
                                        @else
                                            {{ $log->quantity > 0 ? __('added') : __('removed') }} <span class="font-medium text-zinc-700">{{ abs($log->quantity) }}</span> {{ __('units of') }}
                                        @endif
                                    </span>
                                    <span class="font-medium text-zinc-900">{{ $log->product->name }}</span>
                                    @if($log->location)
                                    <span class="text-zinc-500">
                                        {{ $log->type === 'pick' || $log->quantity < 0 ? __('from') : __('at') }}
                                        <span class="font-medium text-zinc-700">{{ implode('/', array_filter([$log->location->zone, $log->location->aisle, $log->location->rack, $log->location->shelf, $log->location->bin])) }}</span>
                                    </span>
                                    @endif
                                </div>
                                <div class="text-xs text-zinc-400 mt-0.5">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-zinc-500 text-sm">{{ __('No recent activity.') }}</div>
                    @endforelse
                </div>
            </flux:card>
        </div>
    </div>
</div>
