<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Inventory Transaction Logs') }}</flux:heading>
            <flux:subheading>{{ __('Immutable audit trail of all warehouse activities.') }}</flux:subheading>
        </div>
        <div class="flex w-full md:w-auto gap-3">
            <flux:select wire:model.live="filter_type" class="w-full md:w-40" placeholder="{{ __('All Types') }}">
                <flux:select.option value="receive">{{ __('Receive (In)') }}</flux:select.option>
                <flux:select.option value="pick">{{ __('Pick (Out)') }}</flux:select.option>
                <flux:select.option value="move">{{ __('Move (Transfer)') }}</flux:select.option>
                <flux:select.option value="count_adjustment">{{ __('Count Adjustment') }}</flux:select.option>
            </flux:select>
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search products, users, refs...') }}" class="w-full md:w-64" />
        </div>
    </div>

    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Timestamp') }}</flux:table.column>
                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                    <flux:table.column>{{ __('Product') }}</flux:table.column>
                    <flux:table.column>{{ __('Location') }}</flux:table.column>
                    <flux:table.column>{{ __('Qty') }}</flux:table.column>
                    <flux:table.column>{{ __('User') }}</flux:table.column>
                    <flux:table.column>{{ __('Notes & Ref') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($transactions as $log)
                        <flux:table.row :key="$log->id">
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium text-sm">{{ $log->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-zinc-500">{{ $log->created_at->format('H:i:s') }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->type === 'receive')
                                    <flux:badge color="success" size="sm" icon="arrow-down-tray">{{ __('Receive') }}</flux:badge>
                                @elseif($log->type === 'pick')
                                    <flux:badge color="warning" size="sm" icon="arrow-up-tray">{{ __('Pick') }}</flux:badge>
                                @elseif($log->type === 'move')
                                    <flux:badge color="blue" size="sm" icon="arrows-right-left">{{ __('Move') }}</flux:badge>
                                @elseif($log->type === 'count_adjustment')
                                    <flux:badge color="zinc" size="sm" icon="adjustments-horizontal">{{ __('Adjust') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium truncate max-w-[150px]">{{ $log->product->name }}</span>
                                    <span class="text-xs text-zinc-500 font-mono">{{ $log->product->sku }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->location)
                                    <span class="text-sm">{{ implode('/', array_filter([$log->location->zone, $log->location->aisle, $log->location->rack])) }}</span>
                                @else
                                    <span class="text-zinc-400 italic">Unknown</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="font-bold {{ $log->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:avatar size="xs" src="{{ $log->user->avatar_url ?? '' }}" />
                                    <span class="text-sm font-medium truncate max-w-[100px]">{{ $log->user->name }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    @if($log->reference)
                                        <span class="text-xs font-mono font-medium text-zinc-700">{{ $log->reference }}</span>
                                    @endif
                                    @if($log->notes)
                                        <span class="text-xs text-zinc-500 truncate max-w-[200px]" title="{{ $log->notes }}">{{ $log->notes }}</span>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500">
                                {{ __('No transaction logs found.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200">
            {{ $transactions->links() }}
        </div>
    </flux:card>
</div>
