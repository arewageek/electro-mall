<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Variance Approvals') }}</flux:heading>
            <flux:subheading>{{ __('Review cycle count discrepancies and authorize inventory adjustments.') }}</flux:subheading>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <flux:select wire:model.live="statusFilter" class="w-full sm:w-32 shrink-0">
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="approved">Approved</flux:select.option>
                <flux:select.option value="rejected">Rejected</flux:select.option>
                <flux:select.option value="all">All Statuses</flux:select.option>
            </flux:select>
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search products...') }}" class="w-full md:w-64" />
        </div>
    </div>

    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Date') }}</flux:table.column>
                    <flux:table.column>{{ __('Product') }}</flux:table.column>
                    <flux:table.column>{{ __('Location') }}</flux:table.column>
                    <flux:table.column>{{ __('Counter') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Expected') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Counted') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Variance') }}</flux:table.column>
                    <flux:table.column class="text-center">{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($variances as $variance)
                        @php
                            $diff = $variance->counted_quantity - $variance->expected_quantity;
                            $diffClass = $diff > 0 ? 'text-green-600' : 'text-red-600';
                            $diffPrefix = $diff > 0 ? '+' : '';
                        @endphp
                        <flux:table.row :key="$variance->id">
                            <flux:table.cell>
                                <span class="text-sm">{{ $variance->created_at->format('M d, Y H:i') }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium">{{ $variance->product->name }}</div>
                                <div class="text-xs text-zinc-500">SKU: {{ $variance->product->sku }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-sm">{{ implode(' / ', array_filter([$variance->location->zone, $variance->location->aisle, $variance->location->rack, $variance->location->shelf, $variance->location->bin])) }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-sm">{{ $variance->counter->name ?? 'Unknown' }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-right font-mono text-zinc-500">
                                {{ $variance->expected_quantity }}
                            </flux:table.cell>
                            <flux:table.cell class="text-right font-mono font-bold">
                                {{ $variance->counted_quantity }}
                            </flux:table.cell>
                            <flux:table.cell class="text-right font-mono font-bold {{ $diffClass }}">
                                {{ $diffPrefix }}{{ $diff }}
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                @if($variance->status === 'pending')
                                    <flux:badge color="amber">Pending</flux:badge>
                                @elseif($variance->status === 'approved')
                                    <flux:badge color="green">Approved</flux:badge>
                                @elseif($variance->status === 'rejected')
                                    <flux:badge color="red">Rejected</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($variance->status === 'pending')
                                    <div class="flex gap-2">
                                        <flux:button size="sm" variant="subtle" icon="x-mark" wire:click="reject('{{ $variance->id }}')" wire:confirm="{{ __('Are you sure you want to reject this variance?') }}">Reject</flux:button>
                                        <flux:button size="sm" variant="primary" icon="check" wire:click="approve('{{ $variance->id }}')" wire:confirm="{{ __('Are you sure you want to approve this variance? This will update the inventory.') }}">Approve</flux:button>
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-500">Resolved by {{ $variance->resolver->name ?? 'Unknown' }}</span>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="9" class="text-center py-8 text-zinc-500">
                                {{ __('No variances found matching your filters.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200">
            {{ $variances->links() }}
        </div>
    </flux:card>
</div>
