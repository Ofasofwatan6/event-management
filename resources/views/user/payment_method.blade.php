@extends('layouts.app')
@section('title', 'Metode Pembayaran | VolunteerHub')

@section('content')
<main class="flex-1 flex justify-center py-6 px-4 lg:px-20 bg-[#f6f8f6]">
    <div class="max-w-5xl w-full flex flex-col md:flex-row gap-6">

        {{-- Sidebar --}}
        <aside class="w-full md:w-64 shrink-0">
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-4 shadow-sm">
                <div class="flex gap-3 items-center border-b pb-3 border-gray-100">
                    <div class="w-10 h-10 bg-[#2F7F79]/20 rounded-full flex items-center justify-center text-[#2F7F79] font-bold">U</div>
                    <div>
                        <h1 class="text-sm font-bold">Relawan Hub</h1>
                        <p class="text-xs text-[#2F7F79] font-medium">🟢 Relawan Aktif</p>
                    </div>
                </div>
                <nav class="flex flex-col gap-1 text-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-2 mb-1">Aktivitas</p>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all text-gray-600"
                       href="{{ route('user.past.events') }}">
                        <span class="material-symbols-outlined text-lg">history</span> Kegiatan Selesai
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all text-gray-600"
                       href="{{ route('user.payment.history') }}">
                        <span class="material-symbols-outlined text-lg">payments</span> Riwayat Pembayaran
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#2F7F79]/10 text-[#2F7F79] font-bold"
                       href="{{ route('user.payment.methods') }}">
                        <span class="material-symbols-outlined text-lg">credit_card</span> Metode Pembayaran
                    </a>
                </nav>
            </div>
        </aside>

        {{-- Konten --}}
        <div class="flex-1 bg-white border border-gray-200 rounded-xl p-5 sm:p-6 shadow-sm">
            <h2 class="text-xl font-bold mb-1">Pilih Metode Pembayaran</h2>
            <p class="text-xs text-gray-500 mb-6">Selesaikan kontribusi pendaftaran Anda dengan aman.</p>

            <div class="space-y-4">
                <div class="border border-gray-200 rounded-xl p-4 hover:border-[#2F7F79] cursor-pointer transition-all flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#2F7F79] text-2xl">account_balance_wallet</span>
                        <div>
                            <h4 class="text-sm font-bold">Dompet Digital (Gopay / OVO / Dana)</h4>
                            <p class="text-xs text-gray-400">Konfirmasi instan otomatis tanpa bukti transfer.</p>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                </div>
                <div class="border border-gray-200 rounded-xl p-4 hover:border-[#2F7F79] cursor-pointer transition-all flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#2F7F79] text-2xl">credit_card</span>
                        <div>
                            <h4 class="text-sm font-bold">Virtual Account Bank (BCA, Mandiri, BNI)</h4>
                            <p class="text-xs text-gray-400">Bisa dibayar dari m-banking mana pun 24 jam.</p>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t pt-4 border-gray-100">
                <button onclick="window.history.back()"
                        class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200">
                    Kembali
                </button>
            </div>
        </div>
    </div>
</main>
@endsection