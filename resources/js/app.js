import { Html5Qrcode, Html5QrcodeScanner } from "html5-qrcode";

window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeScanner = Html5QrcodeScanner;

document.addEventListener('alpine:init', () => {
    window.Alpine.data('barcodeScannerComponent', (containerId) => ({
        scanner: null,
        openScanner() {
            // Need a slight delay to allow modal DOM to be visible for the scanner to bind
            setTimeout(() => {
                this.startScanner();
            }, 200);
        },
        closeScanner() {
            if (this.scanner) {
                this.scanner.clear().catch(error => {
                    console.error("Failed to clear html5QrcodeScanner. ", error);
                });
                this.scanner = null;
            }
        },
        startScanner() {
            if (!this.scanner) {
                this.scanner = new window.Html5QrcodeScanner(
                    containerId,
                    { fps: 10, qrbox: {width: 250, height: 250} },
                    false
                );
                
                this.scanner.render((decodedText, decodedResult) => {
                    // On success
                    this.$dispatch('scan', { code: decodedText });
                    
                    // Close modal
                    if (window.Flux) {
                        window.Flux.modal(`${containerId}-modal`).close();
                    }
                    this.closeScanner();
                }, (errorMessage) => {
                    // Ignore frame errors
                });
            }
        }
    }));
});
