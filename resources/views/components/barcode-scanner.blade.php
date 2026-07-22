@props(['id'])

<div x-data="{
    scanner: null,
    openScanner() {
        setTimeout(() => {
            if (!this.scanner) {
                this.scanner = new window.Html5Qrcode('{{ $id }}');
                
                this.scanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: {width: 250, height: 250} },
                    (decodedText) => {
                        this.$dispatch('scan', { code: decodedText });
                        if (window.Flux) window.Flux.modal('{{ $id }}-modal').close();
                        this.closeScanner();
                    },
                    (errorMessage) => {
                        // ignore frame errors
                    }
                ).catch((err) => {
                    console.error('Camera start failed:', err);
                    alert('Could not start camera. Please ensure you have granted camera permissions and are using a secure connection (HTTPS or localhost).');
                });
            }
        }, 200);
    },
    closeScanner() {
        if (this.scanner && this.scanner.isScanning) {
            this.scanner.stop().then(() => {
                this.scanner.clear();
                this.scanner = null;
            }).catch(e => console.error(e));
        } else if (this.scanner) {
            this.scanner.clear();
            this.scanner = null;
        }
    }
}" {{ $attributes }}>
    <flux:modal.trigger name="{{ $id }}-modal">
        <flux:button variant="ghost" size="sm" icon="qr-code" x-on:click="openScanner" tooltip="{{ __('Scan Barcode') }}"></flux:button>
    </flux:modal.trigger>

    <flux:modal name="{{ $id }}-modal" class="md:w-[500px]" x-on:close="closeScanner">
        <flux:heading>{{ __('Scan Barcode') }}</flux:heading>
        
        <div class="mt-4 bg-black rounded-lg overflow-hidden">
            <div id="{{ $id }}" class="w-full min-h-[300px]"></div>
        </div>

        <div class="flex justify-end mt-4 gap-2">
            <flux:modal.close>
                <flux:button variant="ghost" x-on:click="closeScanner">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>
</div>
