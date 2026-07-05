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
                                        <flux:menu.item icon="printer" wire:click="printLabel('{{ $location->id }}')">{{ __('Print Label') }}</flux:menu.item>
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

    <!-- Print Label Modal -->
    <flux:modal wire:model="show_print_modal" class="md:w-[500px]">
        @if($print_location)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Print Location Label</flux:heading>
                <flux:subheading>Generate a label for the physical location.</flux:subheading>
            </div>

            <div class="p-6 bg-zinc-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center overflow-auto">
                <!-- The printable area -->
                <div id="print-location-area" style="background: white; padding: 24px; text-align: center; border: 1px dashed #ccc; width: 350px; font-family: system-ui, sans-serif;">
                    <div style="font-weight: bold; font-size: 22px; line-height: 1.2; margin-bottom: 2px; color: #000;">
                        {{ $print_location_name }}
                    </div>
                    <div style="font-size: 11px; color: #777; margin-bottom: 16px; letter-spacing: 1px; font-weight: bold; text-transform: uppercase;">
                        LOCATION
                    </div>
                    
                    <div style="width: 120px; height: 120px; margin: 0 auto 16px auto;">
                        <img src="{{ $print_qrcode_svg }}" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;" />
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="display: inline-block; max-width: 100%; overflow: hidden;">
                            {!! $print_barcode_svg !!}
                        </div>
                        <div style="font-family: monospace; font-size: 12px; letter-spacing: 2px; margin-top: 6px; color: #333;">
                            {{ $print_location->barcode }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Close</flux:button>
                </flux:modal.close>
                <flux:button icon="printer" variant="primary" x-on:click="printElement('print-location-area')">Print</flux:button>
            </div>
        </div>
        @endif
    </flux:modal>
    
    @script
    <!-- Print Script (Global to component) -->
    <script>
        // Only define once if not defined by another component
        if (typeof window.printElement === 'undefined') {
            window.printElement = function(elementId) {
                const printContent = document.getElementById(elementId).innerHTML;
                const printWindow = window.open('', '_blank');
                const html = '<html><head><title>Print Label</title><style>@media print { body { margin: 0; padding: 0; background: white; } @page { margin: 0; size: auto; } }</style></head><body style="margin: 0; padding: 20px; display: flex; justify-content: center; background: white;"><div style="width: 4in; min-height: 3in; box-sizing: border-box; page-break-inside: avoid; border: none !important;">' + printContent + '</div></body></html>';
                printWindow.document.write(html);
                printWindow.document.close();
                
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            }
        }
    </script>
    @endscript
</div>
