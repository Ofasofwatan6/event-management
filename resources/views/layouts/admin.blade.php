<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin | VolunteerHub')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Lexend', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f6f8f6] text-slate-900 min-h-screen flex flex-col" x-data="{ mobileNav: false }">

<header class="border-b border-white/10 bg-[#2f7f79] px-4 md:px-8 py-3 sticky top-0 z-50 text-white">
    <div class="max-w-[1200px] mx-auto flex items-center justify-between gap-3">
        <x-site-logo href="{{ route('admin.dashboard') }}" class="h-9 md:h-10 w-auto max-w-[140px] object-contain" textClass="text-white text-base md:text-lg font-bold"/>
        <button type="button" @click="mobileNav = !mobileNav" class="lg:hidden p-2 rounded-lg bg-white/10">
            <span class="material-symbols-outlined" x-text="mobileNav ? 'close' : 'menu'"></span>
        </button>
        <nav class="hidden lg:flex items-center gap-6">
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium hover:opacity-80">Dashboard</a>
            <a href="{{ route('admin.events.index') }}" class="text-sm font-medium hover:opacity-80">Manajemen Kegiatan</a>
            <a href="{{ route('admin.registrations.index') }}" class="text-sm font-medium hover:opacity-80">Manajemen Pendaftaran</a>
            <a href="{{ route('admin.refunds.index') }}" class="text-sm font-medium hover:opacity-80">Refund Management</a>
        </nav>
        <a href="{{ route('admin.profile') }}" class="hidden lg:flex items-center justify-center rounded-lg h-10 w-10 bg-white/10 hover:bg-white/20">
            <span class="material-symbols-outlined">account_circle</span>
        </a>
    </div>
    <nav x-show="mobileNav" x-cloak class="lg:hidden mt-3 pb-2 flex flex-col gap-1 border-t border-white/10 pt-3">
        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm font-medium">Dashboard</a>
        <a href="{{ route('admin.events.index') }}" class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm font-medium">Manajemen Kegiatan</a>
        <a href="{{ route('admin.registrations.index') }}" class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm font-medium">Manajemen Pendaftaran</a>
        <a href="{{ route('admin.refunds.index') }}" class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm font-medium">Refund Management</a>
        <a href="{{ route('admin.profile') }}" class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm font-medium">Profil</a>
    </nav>
</header>

<div class="flex-1 flex flex-col">
    @yield('content')
</div>

<x-admin-footer />
@stack('scripts')
</body>
</html>
