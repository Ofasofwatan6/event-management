<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VolunteerHub - Temukan Kontribusimu')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @stack('styles')
</head>
<body class="bg-[#f6f8f6] text-slate-900 min-h-screen flex flex-col">

    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 bg-[#2f7f79] px-4 md:px-10 py-3 sticky top-0 z-50 text-white">
        <div class="flex items-center gap-4 md:gap-8 min-w-0">
            <x-site-logo href="{{ route('home') }}" class="h-9 w-9 object-contain rounded-lg" textClass="text-white text-lg md:text-xl font-bold hidden sm:inline"/>
            <nav class="hidden md:flex items-center gap-6 lg:gap-9">
                <a href="{{ route('home') }}#events-section" class="text-white text-sm font-medium hover:opacity-80">Cari Kegiatan</a>
                <a href="{{ route('organizations.index') }}" class="text-white text-sm font-medium hover:opacity-80">Organisasi</a>
                <a href="{{ route('terms') }}#faq" class="text-white text-sm font-medium hover:opacity-80">FAQ</a>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $profileUrl }}" class="flex items-center justify-center rounded-lg h-10 w-10 bg-white/10 text-white hover:bg-white/20 transition-all">
                <span class="material-symbols-outlined">account_circle</span>
            </a>
        </div>
    </header>

    <div class="flex-1 flex flex-col">
        @yield('content')
    </div>

    @hasSection('footer')
        @yield('footer')
    @else
        <x-site-footer />
    @endif

    @stack('scripts')
</body>
</html>
