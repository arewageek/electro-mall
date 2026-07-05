<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                @can('shipment.receive')
                <flux:sidebar.group :heading="__('Operations')" class="grid mt-4">
                    <flux:sidebar.item icon="arrow-down-tray" href="#" wire:navigate>
                        {{ __('Receiving') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endcan

                @can('order.pick')
                <flux:sidebar.group :heading="__('Fulfillment')" class="grid mt-4">
                    <flux:sidebar.item icon="arrow-up-tray" href="#" wire:navigate>
                        {{ __('Picking') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endcan

                @canany(['inventory.view', 'product.manage'])
                <flux:sidebar.group :heading="__('Inventory')" class="grid mt-4">
                    <flux:sidebar.item icon="queue-list" :href="route('inventory.stock')" :current="request()->routeIs('inventory.stock')" wire:navigate>
                        {{ __('Stock') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="qr-code" :href="route('inventory.products')" :current="request()->routeIs('inventory.products')" wire:navigate>
                        {{ __('Products') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-check" :href="route('inventory.counts')" :current="request()->routeIs('inventory.counts')" wire:navigate>
                        {{ __('Counts') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endcanany

                @can('location.manage')
                <flux:sidebar.group :heading="__('Warehouse')" class="grid mt-4">
                    <flux:sidebar.item icon="map" :href="route('warehouse.locations')" :current="request()->routeIs('warehouse.locations')" wire:navigate>
                        {{ __('Locations') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="truck" :href="route('warehouse.suppliers')" :current="request()->routeIs('warehouse.suppliers')" wire:navigate>
                        {{ __('Suppliers') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endcan

                @can('user.manage')
                <flux:sidebar.group :heading="__('Administration')" class="grid mt-4">
                    <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                        {{ __('Users') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" href="#" wire:navigate>
                        {{ __('Logs') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endcan
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
