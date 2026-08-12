# Electro Mall - Warehouse Management System

Electro Mall is a web-based Warehouse Management System (WMS) designed to handle inventory, warehouse operations, and personnel for an electronics store.

## Features

- **Inventory Management**: Product catalog, stock tracking, stock counts, and variance approvals.
- **Warehouse Management**: Physical location tracking, label generation, and supplier management.
- **Operations**: Inbound shipment receiving and outbound order picking.
- **Administration**: User management with role-based access control (RBAC) and detailed transaction logging.

## Tech Stack

- **Backend**: Laravel 13, PHP 8.3
- **Frontend**: Livewire 4, Flux UI, Tailwind CSS 4
- **Database**: SQLite (default, easily configurable via `.env`)

## Setup Instructions

Ensure you have **PHP 8.3+**, **Composer**, and **Node.js** installed on your system.

1. **Clone the repository:**
   ```bash
   git clone https://github.com/arewageek/electro-mall.git
   cd electro-mall
   ```

2. **Run the setup script:**
   This project includes a convenient script that installs all PHP and Node dependencies, copies the environment file, generates the application key, runs database migrations, and builds frontend assets.
   ```bash
   composer run setup
   ```

   *(Alternatively, you can run `composer install`, `cp .env.example .env`, `php artisan key:generate`, `php artisan migrate`, `npm install`, and `npm run build` manually).*

3. **Start the development server:**
   ```bash
   composer run dev
   ```
   This command starts the local Laravel server alongside Vite. You can now access the application in your browser at `http://localhost:8000` (or the URL provided in your terminal).

---

> **Note**: This application was built for an academic project. It serves as a proof of concept and educational exercise, and was **not built for scale** or production use.
