<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Warehouse Locations') }}</flux:heading>
            <flux:subheading>{{ __('Manage physical warehouse zones, aisles, racks, and bins.') }}</flux:subheading>
        </div>
        <div class="flex w-full md:w-auto gap-3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search locations...') }}" class="w-full md:w-64" />
            <flux:button variant="primary" wire:click="create" icon="plus" class="shrink-0">{{ __('New Location') }}</flux:button>
        </div>
    </div>

    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Location Path') }}</flux:table.column>
                    <flux:table.column>{{ __('Barcode') }}</flux:table.column>
                    <flux:table.column>{{ __('Zone') }}</flux:table.column>
                    <flux:table.column>{{ __('Aisle') }}</flux:table.column>
                    <flux:table.column>{{ __('Rack') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($locations as $location)
                        <flux:table.row :key="$location->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-zinc-100 flex items-center justify-center text-zinc-500">
                                        <flux:icon.map-pin class="w-5 h-5" />
                                    </div>
                                    <span class="font-medium">
                                        {{ implode(' / ', array_filter([$location->zone, $location->aisle, $location->rack, $location->shelf, $location->bin])) }}
                                    </span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" icon="qr-code" class="font-mono">{{ $location->barcode }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $location->zone }}</flux:table.cell>
                            <flux:table.cell>{{ $location->aisle ?? '-' }}</flux:table.cell>
                            <flux:table.cell>{{ $location->rack ?? '-' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil-square" wire:click="edit('{{ $location->id }}')">{{ __('Edit') }}</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" class="text-red-600 hover:bg-red-50" wire:click="delete('{{ $location->id }}')" wire:confirm="{{ __('Are you sure you want to delete this location?') }}">{{ __('Delete') }}</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                                {{ __('No locations found.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200">
            {{ $locations->links() }}
        </div>
    </flux:card>

    <flux:modal wire:model="show_modal" :heading="$is_editing ? __('Edit Location') : __('Create Location')" class="max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Zone') }}</flux:label>
                    <flux:input wire:model="zone" placeholder="{{ __('e.g., A') }}" />
                    <flux:error name="zone" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Aisle') }}</flux:label>
                    <flux:input wire:model="aisle" placeholder="{{ __('e.g., 01') }}" />
                    <flux:error name="aisle" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Rack') }}</flux:label>
                    <flux:input wire:model="rack" placeholder="{{ __('e.g., 02') }}" />
                    <flux:error name="rack" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Shelf') }}</flux:label>
                    <flux:input wire:model="shelf" placeholder="{{ __('e.g., Top') }}" />
                    <flux:error name="shelf" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Bin') }}</flux:label>
                    <flux:input wire:model="bin" placeholder="{{ __('e.g., B-1') }}" />
                    <flux:error name="bin" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Barcode (Optional)') }}</flux:label>
                <flux:input wire:model="barcode" placeholder="{{ __('Leave blank to auto-generate') }}" />
                <flux:error name="barcode" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
