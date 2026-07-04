<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')
        <style>
            body {
                font-family: 'SF Pro Display', 'Geist Sans', 'Helvetica Neue', 'Switzer', sans-serif;
                background-color: #F7F6F3;
                color: #111111;
            }
        </style>
    </head>
    <body class="min-h-screen antialiased flex flex-col md:flex-row">
        
        <!-- Interactive/Branding Side (Left on Desktop) -->
        <div class="hidden md:flex w-1/2 relative bg-[#FBFBFA] border-r border-[#EAEAEA] flex-col justify-between p-12 overflow-hidden">
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://picsum.photos/seed/warehouse-boxes/1000/1000')] bg-cover bg-center"></div>
            
            <div class="relative z-10 w-full flex justify-start">
                <a href="{{ route('home') }}" class="font-semibold text-2xl tracking-tight" wire:navigate>
                    {{ config('app.name') }}
                </a>
            </div>

            <div class="relative z-10 w-full max-w-md mx-auto flex flex-col justify-center grow">
                <h1 class="text-4xl lg:text-5xl font-medium leading-[1.05] tracking-tight mb-6">
                    Traceability at scale.
                </h1>
                <p class="text-[#787774] text-lg leading-[1.6] mb-12">
                    Enter the warehouse management system to access real-time stock tracking, barcode routing, and fulfillment workflows.
                </p>
                
                <div class="p-5 bg-white rounded-xl flex items-center gap-4 border border-[#EAEAEA] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                    <div class="w-10 h-10 rounded-full bg-[#EDF3EC] text-[#346538] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-sm text-[#111111]">Secure Terminal</h4>
                        <p class="text-xs text-[#787774]">Authorized warehouse personnel only</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Auth Form Side (Right on Desktop, Full on Mobile) -->
        <div class="w-full md:w-1/2 min-h-screen flex flex-col justify-between px-6 py-12 lg:px-24 bg-[#FFFFFF]">
            <a href="{{ route('home') }}" class="md:hidden font-semibold text-xl tracking-tight mb-10 text-center" wire:navigate>
                {{ config('app.name') }}
            </a>
            
            <div class="w-full max-w-sm mx-auto flex flex-col justify-center grow">
                {{ $slot }}
            </div>
            
            <p class="text-center md:text-left text-xs text-[#787774] mt-12 font-mono tracking-wide mx-auto max-w-sm w-full">
                {{ config('app.name') }} &copy; {{ date('Y') }}
            </p>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
