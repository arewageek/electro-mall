<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 print:hidden">
        <div>
            <flux:heading size="xl" level="1">{{ __('Label Generation') }}</flux:heading>
            <flux:subheading>{{ __('Generate and print physical Barcode and QR labels for Products and Locations.') }}</flux:subheading>
        </div>
        <div class="flex w-full md:w-auto gap-3 items-center">
            <flux:select wire:model.live="type" class="w-32">
                <flux:select.option value="product">Products</flux:select.option>
                <flux:select.option value="location">Locations</flux:select.option>
            </flux:select>
            
            <flux:select wire:model.live="format" class="w-32">
                <flux:select.option value="barcode">1D Barcode</flux:select.option>
                <flux:select.option value="qrcode">2D QR Code</flux:select.option>
            </flux:select>
            
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search...') }}" class="w-full md:w-64" />
        </div>
    </div>

    <!-- Main List -->
    <flux:card class="p-0 print:hidden relative">
        @if(count($selected_items) > 0)
            <div class="absolute top-0 left-0 w-full bg-blue-50 border-b border-blue-100 p-3 flex justify-between items-center z-10 rounded-t-xl">
                <span class="text-sm font-medium text-blue-800">{{ count($selected_items) }} items selected</span>
                <div class="flex gap-2">
                    <flux:button size="sm" variant="subtle" wire:click="$set('selected_items', [])">Clear</flux:button>
                    <flux:button size="sm" variant="primary" icon="printer" wire:click="generateBulkLabels">Print Selected</flux:button>
                </div>
            </div>
        @endif
        
        <div class="overflow-x-auto {{ count($selected_items) > 0 ? 'mt-14' : '' }}">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="w-10"></flux:table.column>
                    <flux:table.column>{{ $type === 'product' ? __('Product Name') : __('Location Path') }}</flux:table.column>
                    <flux:table.column>{{ __('Barcode Code') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($items as $item)
                        @php
                            $text = $type === 'product' ? ($item->barcode ?: $item->sku) : $item->barcode;
                            $title = $type === 'product' ? $item->name : implode(' / ', array_filter([$item->zone, $item->aisle, $item->rack, $item->shelf, $item->bin]));
                        @endphp
                        <flux:table.row :key="$item->id">
                            <flux:table.cell>
                                <flux:checkbox wire:model="selected_items" :value="$item->id" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="font-medium">{{ $title }}</span>
                                @if($type === 'product')
                                    <div class="text-xs text-zinc-500">SKU: {{ $item->sku }}</div>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="font-mono font-medium">{{ $text }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="sm" variant="subtle" icon="qr-code" wire:click="generateLabel({{ $item->id }})">
                                    {{ __('Generate') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center py-8 text-zinc-500">
                                {{ __('No records found.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200">
            {{ $items->links() }}
        </div>
    </flux:card>

    <!-- Print Modal -->
    <flux:modal wire:model="show_print_modal" :heading="__('Print Labels')" class="md:w-[800px] print:hidden">
        <div class="space-y-4">
            <div class="text-sm text-zinc-500 mb-4">
                Verify the labels below. When ready, click Print to send to your thermal label printer.
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[60vh] overflow-y-auto p-2 bg-zinc-50 rounded-lg border border-zinc-200">
                @foreach($print_labels as $label)
                    <div class="bg-white border border-zinc-300 rounded-lg p-4 flex flex-col items-center justify-center text-center shadow-sm">
                        <div class="font-bold text-sm mb-1 line-clamp-1">{{ $label['title'] }}</div>
                        <div class="text-xs text-zinc-500 mb-3">{{ $label['subtitle'] }}</div>
                        
                        @if($label['image'])
                            @if($format === 'barcode')
                                <img src="{{ $label['image'] }}" alt="Barcode" class="max-w-full h-16 object-contain mb-2" />
                            @else
                                <div class="w-32 h-32 mb-2 flex justify-center items-center">
                                    {!! $label['image'] !!}
                                </div>
                            @endif
                        @else
                            <div class="text-red-500 text-xs">Failed to generate</div>
                        @endif
                        
                        <div class="font-mono text-xs">{{ $label['text'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" icon="printer" onclick="window.print()">{{ __('Print Now') }}</flux:button>
            </div>
        </div>
    </flux:modal>
    
    <!-- Print Styles / Printable Area -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .print-label {
                width: 4in;
                height: 2in;
                padding: 10px;
                border: 1px solid #ccc;
                page-break-inside: avoid;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
            }
            .print-label img, .print-label svg {
                max-width: 90%;
                max-height: 1.2in;
            }
        }
    </style>
    
    <div class="hidden print:flex print-area">
        @foreach($print_labels as $label)
            <div class="print-label">
                <div style="font-weight: bold; font-size: 14px; margin-bottom: 4px;">{{ $label['title'] }}</div>
                <div style="font-size: 10px; color: #666; margin-bottom: 8px;">{{ $label['subtitle'] }}</div>
                @if($label['image'])
                    @if($format === 'barcode')
                        <img src="{{ $label['image'] }}" style="height: 50px; object-fit: contain; margin-bottom: 4px;" />
                    @else
                        <div style="width: 80px; height: 80px; margin-bottom: 4px;">
                            {!! $label['image'] !!}
                        </div>
                    @endif
                @endif
                <div style="font-family: monospace; font-size: 12px;">{{ $label['text'] }}</div>
            </div>
        @endforeach
    </div>
</div>
