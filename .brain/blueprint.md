# Technical Blueprint

## 1. Technical Stack & Architecture
While the initial documentation outlined a Python/Tkinter desktop application, the actual implemented codebase is architected as a modern, responsive web application using the Laravel ecosystem:
- **Frontend**: Livewire 4, Flux UI, Alpine.js, TailwindCSS 4 (bundled via Vite)
- **Backend**: Laravel 13 (PHP 8.3)
- **Database**: SQLite (configured natively for simple deployment, with schema ready for MySQL/PostgreSQL scaling)
- **Testing**: Pest PHP
- **Linting/Formatting**: Laravel Pint, PHPStan (Larastan)

## 2. Design System & Identity Standards
- **UI Framework**: Flux UI components integrated with Tailwind CSS 4.
- **Visual Tone**: Professional, efficient, and data-dense for a warehouse environment. High contrast and large tap targets for mobile/scanner readability.
- **Key UI Patterns**: Responsive grids for dashboards, robust data tables for inventory, and streamlined forms for transaction entry.

## 3. Database Schema (Core Entities)
- **Primary Keys**: All system schemas and pivot tables exclusively use `UUIDs` instead of auto-incrementing integers to ensure high scalability and distributed system compatibility.
- **User**: Authentication, role-based access control (Admin, Manager, Clerk, Picker, Receiving). Uses `first_name` and `last_name` with a dynamic `name()` attribute.
- **Category**: Product classifications.
- **Product**: Electronic goods catalog (SKU, names, specifications, barcode/QR data).
- **Location**: Warehouse storage addresses (zone, aisle, rack, shelf, bin).
- **Inventory**: Real-time stock levels linking Product and Location.
- **Supplier**: Vendor details.
- **Transaction**: Audit trail of all stock movements (receipts, picks, counts).
- **PurchaseOrder & POItem**: Incoming shipments and their line items.
- **Order & OrderItem**: Customer orders and fulfillment requirements.

## 4. Core Application Flows
1. **Receiving Process**:
   - Shipment arrives -> Validate against Purchase Order.
   - Inspect items -> Generate Barcode/QR Code -> Print labels.
   - System assigns put-away location -> Physical put-away -> Scan confirmation -> Inventory updated.
2. **Order Picking Process**:
   - Order received -> Pick list generated with optimized routing.
   - Picker navigates to location -> Scans location -> Scans item barcode.
   - Quantity confirmed -> Order packed -> Inventory deducted.
3. **Cycle Counting**:
   - Schedule generated -> Clerk navigates to location -> Scans items sequentially.
   - System compares scan vs expected -> Variance approved/rejected by Manager -> Inventory adjusted.

## 5. Development Standards
- **Component Design**: Favor Flux UI components for consistent forms, tables, and modals.
- **State Management**: Livewire components for server-side state coordination; Alpine.js for lightweight, immediate client interactions (e.g., handling hardware barcode scanner input).
- **Validation**: Strict Form Requests or native Livewire rule validation to ensure inventory data integrity.
- **Routing**: Named routes utilizing Livewire full-page components where applicable for SPAs-like transitions.
