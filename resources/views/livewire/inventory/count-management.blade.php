<div x-data="{
    barcode: '',
    lastScanTime: 0,
    cameraActive: false,
    scannerObj: null,
    
    init() {
        // Hardware Scanner / Keyboard Listener
        window.addEventListener('keypress', (e) => {
            if (!$wire.scanner_mode) return;
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;
            
            const now = new Date().getTime();
            // Increased to 500ms so humans typing on a keyboard can test it
            if (now - this.lastScanTime > 500) this.barcode = '';
            this.lastScanTime = now;
            
            if (e.key === 'Enter') {
                if (this.barcode.length > 0) {
                    $wire.handleScan(this.barcode);
                    this.barcode = '';
                    e.preventDefault();
                }
            } else {
                this.barcode += e.key;
            }
        });
    },

    startCamera() {
        this.cameraActive = true;
        this.$nextTick(() => {
            if (typeof Html5Qrcode === 'undefined') {
                console.error('Html5Qrcode is not loaded.');
                return;
            }
            
            this.scannerObj = new Html5Qrcode('reader');
            
            let lastScannedText = '';
            let lastScannedTime = 0;

            const startScan = (cameraConfig) => {
                this.scannerObj.start(
                    cameraConfig,
                    {
                        fps: 10,
                        qrbox: { width: 350, height: 200 }, // Generous wide box for 1D barcodes
                        disableFlip: false // Allows the scanning engine to read backwards/mirrored frames in memory
                    },
                    (decodedText) => {
                        const now = new Date().getTime();
                        if (decodedText !== lastScannedText || (now - lastScannedTime) > 2000) {
                            console.log('SUCCESSFUL SCAN:', decodedText);
                            
                            // Visual feedback: Flash the reader box green
                            const readerEl = document.getElementById('reader');
                            if(readerEl) {
                                readerEl.style.border = '4px solid #22c55e';
                                setTimeout(() => { readerEl.style.border = '2px solid #e4e4e7'; }, 400);
                            }

                            $wire.handleScan(decodedText);
                            lastScannedText = decodedText;
                            lastScannedTime = now;
                        }
                    },
                    (errorMessage) => {
                        // ignore frame errors
                    }
                ).catch((err) => {
                    console.error('Camera start failed:', err);
                    alert('Failed to start camera. Please check permissions.');
                    this.cameraActive = false;
                });
            };

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length > 0) {
                    // Attempt to find a back/environment camera, else default to the first one available
                    let backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment'));
                    let cameraId = backCamera ? backCamera.id : devices[0].id;
                    startScan(cameraId);
                } else {
                    startScan({ facingMode: 'environment' });
                }
            }).catch(err => {
                startScan({ facingMode: 'environment' });
            });
        });
    },

    stopCamera() {
        if (this.scannerObj) {
            this.scannerObj.stop().then(() => {
                this.scannerObj.clear();
                this.scannerObj = null;
            }).catch(err => {
                console.error('Failed to stop camera:', err);
            });
        }
        this.cameraActive = false;
    }
}">
    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    @endpush

    @if($scanner_mode)
        <style>
            /* Force the video feed to NOT act like a mirror */
            #reader video {
                transform: none !important;
            }
        </style>

        <!-- Fast Scanner UI -->
        <div class="fixed inset-0 bg-white z-50 flex flex-col p-6 overflow-y-auto">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                        <flux:icon.qr-code class="w-6 h-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900">Continuous Scanner</h1>
                        <p class="text-zinc-500">Scan via hardware gun, or use your device camera.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <template x-if="!cameraActive">
                        <flux:button variant="primary" icon="camera" x-on:click="startCamera()">Start Camera</flux:button>
                    </template>
                    <template x-if="cameraActive">
                        <flux:button variant="danger" icon="stop" x-on:click="stopCamera()">Stop Camera</flux:button>
                    </template>
                    <flux:button variant="subtle" icon="x-mark" wire:click="toggleScannerMode" x-on:click="stopCamera()">Exit</flux:button>
                </div>
            </div>

            <!-- Camera Viewfinder -->
            <div x-show="cameraActive" class="mb-6 flex justify-center">
                <div id="reader" wire:ignore class="w-full max-w-md border-2 border-zinc-200 rounded-xl overflow-hidden bg-zinc-50"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 flex-1">
                <!-- Location Panel -->
                <div class="flex flex-col gap-4">
                    <div class="p-6 rounded-2xl border-2 {{ $current_location_id ? 'border-green-500 bg-green-50' : 'border-dashed border-zinc-300 bg-zinc-50' }} flex flex-col items-center justify-center text-center h-48 transition-colors">
                        @if($current_location_id)
                            <flux:icon.check-circle class="w-12 h-12 text-green-500 mb-2" />
                            <h2 class="text-xl font-bold text-green-900">Location Locked</h2>
                            <p class="text-green-700 font-mono text-lg mt-1">{{ $current_location_name }}</p>
                        @else
                            <flux:icon.map-pin class="w-12 h-12 text-zinc-400 mb-2" />
                            <h2 class="text-xl font-bold text-zinc-700">1. Scan Location Barcode</h2>
                            <p class="text-zinc-500">Waiting for location scan...</p>
                        @endif
                    </div>

                    @if($current_location_id)
                        <div class="flex justify-center">
                            <flux:button variant="subtle" wire:click="$set('current_location_id', null)">Clear Location</flux:button>
                        </div>
                    @endif
                </div>

                <!-- Product Panel -->
                <div class="flex flex-col gap-4">
                    <div class="p-6 rounded-2xl border-2 {{ $current_product_id ? 'border-blue-500 bg-blue-50' : 'border-dashed border-zinc-300 bg-zinc-50' }} flex flex-col items-center justify-center text-center h-48 transition-colors">
                        @if($current_product_id)
                            <flux:icon.cube class="w-12 h-12 text-blue-500 mb-2" />
                            <h2 class="text-xl font-bold text-blue-900">{{ $current_product_name }}</h2>
                            <p class="text-blue-700 font-medium mt-1">Expected: {{ $current_expected }}</p>
                        @else
                            <flux:icon.cube class="w-12 h-12 text-zinc-400 mb-2" />
                            <h2 class="text-xl font-bold text-zinc-700">2. Scan Product Barcode</h2>
                            <p class="text-zinc-500">Scan product to increment count</p>
                        @endif
                    </div>

                    @if($current_product_id)
                        <div class="flex items-center justify-center gap-6 mt-4">
                            <flux:button variant="subtle" icon="minus" wire:click="decrementFastCount" class="w-16 h-16 rounded-full" />
                            <div class="flex flex-col items-center">
                                <span class="text-sm text-zinc-500 uppercase tracking-widest font-bold">Count</span>
                                <input type="number" wire:model.blur="current_count" class="text-6xl font-black text-center bg-transparent border-0 ring-0 focus:ring-0 w-32 outline-none" min="0" />
                            </div>
                            <flux:button variant="primary" icon="plus" wire:click="incrementFastCount" class="w-16 h-16 rounded-full" />
                        </div>
                        <div class="flex justify-center mt-8">
                            <flux:button variant="primary" icon="check" wire:click="saveCurrentFastCount" class="w-full max-w-sm">Save & Scan Next</flux:button>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="mt-auto pt-6 text-center text-zinc-400 text-sm">
                Pro Tip: Scanning a different product or location will automatically save your current count.
            </div>
        </div>
    @else
        <!-- Standard UI View -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <flux:heading size="xl" level="1">{{ __('Inventory Counts') }}</flux:heading>
                <flux:subheading>{{ __('Reconcile physical inventory against expected system quantities.') }}</flux:subheading>
            </div>
            <div class="w-full md:w-auto flex gap-3">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search inventory...') }}" class="w-full md:w-64" />
                <flux:button variant="primary" wire:click="toggleScannerMode" icon="qr-code" class="shrink-0">{{ __('Continuous Scanner Mode') }}</flux:button>
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
    @endif
</div>
