<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Activity Reports') }}</flux:heading>
            <flux:subheading>{{ __('Generate structured reports for warehouse transactions.') }}</flux:subheading>
        </div>
        <div class="flex gap-3">
            <flux:button wire:click="exportCsv" icon="arrow-down-tray" variant="primary">{{ __('Export CSV') }}</flux:button>
        </div>
    </div>

    <flux:card class="mb-6">
        <form wire:submit.prevent="$refresh" class="flex flex-col gap-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <flux:input type="date" wire:model.live="start_date" label="{{ __('Start Date') }}" />
                
                <flux:input type="date" wire:model.live="end_date" label="{{ __('End Date') }}" />
                
                <div class="md:col-span-2">
                    <flux:select wire:model.live="filter_type" label="{{ __('Activity Type') }}" placeholder="{{ __('All Types (Full Report)') }}">
                        <flux:select.option value="receive">{{ __('Receive (In)') }}</flux:select.option>
                        <flux:select.option value="pick">{{ __('Pick (Out)') }}</flux:select.option>
                        <flux:select.option value="move">{{ __('Move (Transfer)') }}</flux:select.option>
                        <flux:select.option value="count_adjustment">{{ __('Count Adjustment') }}</flux:select.option>
                    </flux:select>
                </div>
                
                <div class="md:col-span-2 lg:col-span-1">
                    <flux:select wire:model.live="category_id" label="{{ __('Category') }}" placeholder="{{ __('All Categories') }}">
                        @foreach($this->categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                
                <div class="md:col-span-2 lg:col-span-3">
                    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" label="{{ __('Search') }}" placeholder="{{ __('Products, users, refs...') }}" />
                </div>
            </div>
            
            <div class="flex justify-end mt-2">
                <flux:button wire:click="clearFilters" size="sm" variant="subtle">{{ __('Clear Filters') }}</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Date') }}</flux:table.column>
                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                    <flux:table.column>{{ __('Category') }}</flux:table.column>
                    <flux:table.column>{{ __('Product') }}</flux:table.column>
                    <flux:table.column>{{ __('Qty') }}</flux:table.column>
                    <flux:table.column>{{ __('User') }}</flux:table.column>
                    <flux:table.column>{{ __('Ref / Notes') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($transactions as $log)
                        <flux:table.row :key="$log->id">
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium text-sm whitespace-nowrap">{{ $log->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-zinc-500">{{ $log->created_at->format('H:i') }}</span>
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
                                <span class="text-sm">{{ $log->product->category->name ?? 'N/A' }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium truncate max-w-[150px]">{{ $log->product->name }}</span>
                                    <span class="text-xs text-zinc-500 font-mono">{{ $log->product->sku }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="font-bold {{ $log->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-sm font-medium">{{ $log->user->name ?? 'System' }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    @if($log->reference)
                                        <span class="text-xs font-mono font-medium text-zinc-700">{{ $log->reference }}</span>
                                    @endif
                                    @if($log->notes)
                                        <span class="text-xs text-zinc-500 truncate max-w-[150px]" title="{{ $log->notes }}">{{ $log->notes }}</span>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500">
                                {{ __('No activities found matching criteria.') }}
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
