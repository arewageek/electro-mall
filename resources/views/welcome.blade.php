<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WMS</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <style>
        body {
            font-family: 'SF Pro Display', 'Geist Sans', 'Helvetica Neue', 'Switzer', sans-serif;
            background-color: #F7F6F3;
            color: #111111;
        }
        .bento-card {
            border: 1px solid #EAEAEA;
            background: #FFFFFF;
        }
    </style>
</head>
<body class="overflow-x-hidden w-full max-w-full antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-[#F7F6F3]/80 backdrop-blur-md border-b border-[#EAEAEA]">
        <div class="max-w-7xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between">
            <span class="font-semibold text-lg md:text-xl tracking-tight">WMS</span>
            <div class="flex gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 md:px-5 py-2 text-sm bg-[#111111] text-white rounded active:scale-[0.98] transition-transform shadow-sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 md:px-5 py-2 text-sm bg-[#111111] text-white rounded active:scale-[0.98] transition-transform shadow-sm">
                        Log in
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero / Attention -->
    <section class="min-h-[100dvh] pt-28 pb-16 md:pt-32 md:pb-24 lg:py-48 px-4 md:px-6 flex flex-col justify-center">
        <div class="max-w-7xl mx-auto w-full flex flex-col md:flex-row gap-10 md:gap-12 items-center">
            <div class="w-full md:w-1/2 flex flex-col justify-center text-center md:text-left">
                <h1 class="max-w-4xl text-[clamp(2.5rem,8vw,5.5rem)] font-medium leading-[1.05] tracking-tight mb-4 md:mb-6">
                    Warehouse management, <br class="hidden md:block"/>
                    simplified.
                </h1>
                <p class="text-[#787774] text-base md:text-lg max-w-xl mx-auto md:mx-0 leading-[1.6] mb-8 md:mb-10">
                    Replace manual logbooks with real-time barcode and QR scanning. Built specifically for WMS to track items accurately and efficiently.
                </p>
                <div class="flex gap-4 justify-center md:justify-start">
                    <a href="#features" class="px-6 py-3 bg-[#111111] text-white rounded font-medium active:scale-[0.98] transition-transform w-full md:w-auto text-center">
                        Explore Features
                    </a>
                </div>
            </div>
            
            <!-- GSAP Reveal Card (Human Readable UI) -->
            <div class="w-full md:w-1/2 relative min-h-[300px] md:min-h-[400px] bento-card rounded-xl overflow-hidden flex flex-col items-center justify-center p-6 md:p-8 gs-hero-card shadow-sm bg-[#FBFBFA]">
                <div class="absolute inset-0 opacity-[0.03] bg-[url('https://picsum.photos/seed/warehouse-boxes/1000/1000')] bg-cover bg-center"></div>
                
                <div class="relative z-10 w-full max-w-sm flex flex-col gap-3 md:gap-4">
                    <!-- Notification Card 1 -->
                    <div class="p-3 md:p-4 bg-white rounded-xl flex items-center gap-3 md:gap-4 border border-[#EAEAEA] shadow-sm">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-[#EDF3EC] text-[#346538] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-xs md:text-sm text-[#111111]">Samsung Galaxy S24</h4>
                            <p class="text-[10px] md:text-xs text-[#787774]">Scanned and added to inventory</p>
                        </div>
                    </div>
                    
                    <!-- Notification Card 2 -->
                    <div class="p-3 md:p-4 bg-white rounded-xl flex items-center gap-3 md:gap-4 border border-[#EAEAEA] shadow-sm">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-[#E1F3FE] text-[#1F6C9F] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-xs md:text-sm text-[#111111]">Order #1042 Picked</h4>
                            <p class="text-[10px] md:text-xs text-[#787774]">Items ready for dispatch</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bento Grid / Interest -->
    <section id="features" class="py-16 md:py-24 lg:py-32 px-4 md:px-6 bg-[#FFFFFF] border-t border-[#EAEAEA]">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-12 grid-flow-dense gap-4 md:gap-6">
                <!-- Main Feature Card -->
                <div class="col-span-1 md:col-span-8 md:row-span-2 bento-card p-6 md:p-10 rounded-xl gs-fade-up flex flex-col">
                    <h3 class="text-xl md:text-2xl font-medium mb-3 md:mb-4 tracking-tight">Real-time stock tracking</h3>
                    <p class="text-[#787774] text-sm md:text-base max-w-md leading-[1.6] mb-6 md:mb-8">
                        Monitor every electronic product from the moment it arrives to the moment it leaves. Say goodbye to lost items and manual stock counting errors.
                    </p>
                    <div class="w-full grow min-h-[200px] md:min-h-[250px] bg-[#F7F6F3] border border-[#EAEAEA] rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="/images/warehouse-agent.png" alt="Warehouse agent scanning stock" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" />
                    </div>
                </div>
                
                <!-- Side Feature Card 1 -->
                <div class="col-span-1 md:col-span-4 md:row-span-1 bento-card p-6 md:p-8 rounded-xl gs-fade-up flex flex-col justify-center">
                    <span class="inline-block self-start px-3 py-1 bg-[#E1F3FE] text-[#1F6C9F] text-[10px] md:text-xs font-medium rounded-full mb-4 md:mb-6">Fast</span>
                    <h3 class="text-lg md:text-xl font-medium mb-2 tracking-tight">Barcode & QR Ready</h3>
                    <p class="text-[#787774] text-xs md:text-sm leading-[1.6]">
                        Scan items instantly using mobile devices. Process incoming shipments and customer orders much faster than writing on paper.
                    </p>
                </div>
                
                <!-- Side Feature Card 2 -->
                <div class="col-span-1 md:col-span-4 md:row-span-1 bento-card p-6 md:p-8 rounded-xl gs-fade-up flex flex-col justify-center">
                    <span class="inline-block self-start px-3 py-1 bg-[#FDEBEC] text-[#9F2F2D] text-[10px] md:text-xs font-medium rounded-full mb-4 md:mb-6">Accurate</span>
                    <h3 class="text-lg md:text-xl font-medium mb-2 tracking-tight">Eliminate Human Error</h3>
                    <p class="text-[#787774] text-xs md:text-sm leading-[1.6]">
                        The system automatically checks every scan to ensure the correct products are stored and picked.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Scroll Pinning / Desire -->
    <section class="py-16 md:py-32 px-4 md:px-6 gs-pin-container overflow-hidden bg-[#F7F6F3] border-t border-[#EAEAEA]">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-10 md:gap-12">
            <div class="w-full md:w-1/2 gs-pin-text">
                <h2 class="text-3xl md:text-4xl font-medium tracking-tight mb-4 md:mb-6 text-center md:text-left">A clear workflow.</h2>
                <p class="text-[#787774] text-base md:text-lg max-w-md mx-auto md:mx-0 leading-[1.6] gs-scrub-text text-center md:text-left">
                    From receiving new shipments to picking customer orders, the system guides your staff through every daily task clearly and simply.
                </p>
            </div>
            
            <div class="w-full md:w-1/2 flex flex-col gap-6 md:gap-8 gs-scroll-images">
                <div class="bento-card p-6 md:p-8 rounded-xl h-auto md:h-56 flex flex-col justify-center border-l-4 border-l-[#111111]">
                    <span class="text-xs md:text-sm font-medium text-[#787774] mb-2 uppercase tracking-wide">Step 1</span>
                    <h3 class="text-xl md:text-2xl font-medium">Receive & Scan Goods</h3>
                    <p class="mt-2 text-[#787774] text-sm md:text-base">Scan new arrivals to instantly log them into the system.</p>
                </div>
                <div class="bento-card p-6 md:p-8 rounded-xl h-auto md:h-56 flex flex-col justify-center border-l-4 border-l-[#111111]">
                    <span class="text-xs md:text-sm font-medium text-[#787774] mb-2 uppercase tracking-wide">Step 2</span>
                    <h3 class="text-xl md:text-2xl font-medium">Store on Shelves</h3>
                    <p class="mt-2 text-[#787774] text-sm md:text-base">The system tells you exactly which aisle and shelf to use.</p>
                </div>
                <div class="bento-card p-6 md:p-8 rounded-xl h-auto md:h-56 flex flex-col justify-center border-l-4 border-l-[#111111]">
                    <span class="text-xs md:text-sm font-medium text-[#787774] mb-2 uppercase tracking-wide">Step 3</span>
                    <h3 class="text-xl md:text-2xl font-medium">Pick for Orders</h3>
                    <p class="mt-2 text-[#787774] text-sm md:text-base">Scan items as you fulfill orders to ensure zero mistakes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer / Action -->
    <footer class="py-24 md:py-40 px-4 md:px-6 bg-[#FFFFFF] border-t border-[#EAEAEA] flex flex-col items-center justify-center text-center">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-medium tracking-tight mb-8 md:mb-10">Start managing your inventory</h2>
        @auth
            <a href="{{ route('dashboard') }}" class="px-6 md:px-8 py-3 md:py-4 bg-[#111111] text-white rounded font-medium active:scale-[0.98] transition-transform text-base md:text-lg shadow-sm w-full sm:w-auto">
                Enter Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="px-6 md:px-8 py-3 md:py-4 bg-[#111111] text-white rounded font-medium active:scale-[0.98] transition-transform text-base md:text-lg shadow-sm w-full sm:w-auto">
                Log in to WMS
            </a>
        @endauth
        <p class="text-[#787774] text-xs md:text-sm mt-12 md:mt-16 tracking-wide">WMS &copy; {{ date('Y') }}</p>
    </footer>

    <!-- GSAP Motion Choreography -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            gsap.registerPlugin(ScrollTrigger);

            // 1. Initial Hero Physics Reveal
            gsap.from(".gs-hero-card", {
                y: 60,
                opacity: 0,
                duration: 1.4,
                ease: "power3.out"
            });

            // 2. Cascade Staggered Card Entrances
            gsap.utils.toArray('.gs-fade-up').forEach((el, index) => {
                gsap.from(el, {
                    scrollTrigger: {
                        trigger: el,
                        start: "top 90%", // Trigger slightly later on mobile
                    },
                    y: 30,
                    opacity: 0,
                    duration: 1.2,
                    ease: "power3.out",
                    delay: index * 0.1
                });
            });

            // 3. Scroll Pinning Architecture (Desktop Only)
            if (window.innerWidth >= 768) {
                ScrollTrigger.create({
                    trigger: ".gs-pin-container",
                    start: "top 15%",
                    end: "bottom 85%",
                    pin: ".gs-pin-text",
                    pinSpacing: false
                });
            }

            // 4. Typographic Scrub Reveal (Desktop Only for better mobile performance)
            if (window.innerWidth >= 768) {
                const textEl = document.querySelector('.gs-scrub-text');
                if (textEl) {
                    gsap.fromTo(textEl, 
                        { opacity: 0.2 },
                        {
                            opacity: 1,
                            scrollTrigger: {
                                trigger: ".gs-pin-container",
                                start: "top center",
                                end: "center center",
                                scrub: true
                            }
                        }
                    );
                }
            }
        });
    </script>
</body>
</html>
