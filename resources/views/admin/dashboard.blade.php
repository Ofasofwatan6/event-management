@extends('layouts.admin')
@section('title', 'Dashboard Admin | VolunteerHub')

@section('content')
<div x-data="{
    searchQuery: '',
    events: @js($eventsJson),
    get filteredEvents() {
        return this.events.filter(e =>
            e.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            e.org.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
    },
    getTotalRegistered() { return this.events.reduce((acc, e) => acc + e.registered, 0); },
    deleteEvent(id) {
        if(!confirm('Hapus kegiatan ini?')) return;
        const form = document.getElementById('delete-event-form-' + id);
        if (form) form.submit();
    }
}">

<main class="max-w-[1200px] mx-auto w-full px-4 md:px-6 py-6 md:py-10 flex-grow">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Dashboard Admin</h1>
            <p class="text-slate-500 font-medium">Selamat datang kembali, Admin.</p>
        </div>
        <a href="{{ route('admin.events.index') }}"
           class="w-full md:w-auto bg-[#2F7F79] text-white px-6 py-3 rounded-xl font-black shadow-lg hover:scale-105 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">add_circle</span> Buat Kegiatan Baru
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-12">
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-zinc-100 shadow-sm flex items-center gap-6 group hover:border-[#2F7F79] transition-all">
            <div class="size-14 rounded-2xl bg-[#2F7F79]/10 text-[#2F7F79] flex items-center justify-center group-hover:bg-[#2F7F79] group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">event_available</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Kegiatan</p>
                <p class="text-3xl font-black text-slate-900" x-text="events.length"></p>
            </div>
        </div>
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-zinc-100 shadow-sm flex items-center gap-6 group hover:border-[#2F7F79] transition-all">
            <div class="size-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">group</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Peserta Terdaftar</p>
                <p class="text-3xl font-black text-slate-900" x-text="getTotalRegistered()"></p>
            </div>
        </div>
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-zinc-100 shadow-sm flex items-center gap-6 group hover:border-[#2F7F79] transition-all">
            <div class="size-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">hourglass_empty</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kegiatan Mendatang</p>
                <p class="text-3xl font-black text-slate-900">{{ $stats['upcoming_events'] ?? 0 }} <span class="text-sm font-medium text-slate-400">Mendatang</span></p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-zinc-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-zinc-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="font-black text-xl">Daftar Kegiatan</h3>
            <div class="relative w-full md:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                <input type="text" x-model="searchQuery" placeholder="Cari kegiatan..."
                       class="w-full pl-10 pr-4 py-2 bg-[#f6f8f6] border-none rounded-xl text-sm focus:ring-2 focus:ring-[#2F7F79]/20"/>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#f6f8f6]/50 border-b border-zinc-50 text-xs font-black uppercase text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-8 py-4">Nama Kegiatan</th>
                        <th class="px-8 py-4">Kuota</th>
                        <th class="px-8 py-4">Tipe</th>
                        <th class="px-8 py-4">Jadwal</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50">
                    <template x-if="filteredEvents.length === 0">
                        <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">Tidak ada kegiatan yang cocok.</td></tr>
                    </template>
                    <template x-for="event in filteredEvents" :key="event.id">
                        <tr class="hover:bg-[#f6f8f6]/30 transition-all">
                            <td class="px-8 py-6">
                                <p class="font-bold" x-text="event.title"></p>
                                <p class="text-xs text-[#2F7F79] font-medium" x-text="event.org"></p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex-grow bg-slate-100 h-2 rounded-full overflow-hidden max-w-[80px]">
                                        <div class="bg-[#2F7F79] h-full" :style="`width:${(event.registered/event.quota)*100}%`"></div>
                                    </div>
                                    <span class="text-sm font-black text-slate-700 whitespace-nowrap">
                                        <span x-text="event.registered"></span>/<span x-text="event.quota"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-2 py-1 bg-slate-100 text-[10px] font-black uppercase rounded-lg" x-text="event.type"></span>
                            </td>
                            <td class="px-8 py-6 text-sm font-bold text-slate-600" x-text="event.date"></td>
                            <td class="px-8 py-6">
                                <span :class="{
                                    'bg-green-100 text-green-700': event.status === 'Berjalan',
                                    'bg-blue-100 text-blue-700': event.status === 'Mendatang',
                                    'bg-orange-100 text-orange-700': event.status === 'Penuh'
                                }" class="px-2 py-1 text-[10px] font-black uppercase rounded-lg" x-text="event.status"></span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex justify-center gap-2">
                                    <a :href="'{{ route('admin.events.index') }}?edit=' + event.id" class="p-2 hover:bg-blue-50 text-blue-600 rounded-lg transition-all">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    <form :id="'delete-event-form-' + event.id" method="POST" :action="'{{ url('/admin/events') }}/' + event.id" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="deleteEvent(event.id)" class="p-2 hover:bg-red-50 text-red-600 rounded-lg transition-all">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 border-t border-zinc-50 bg-[#f6f8f6]/20 flex justify-between items-center text-sm font-medium text-slate-400">
            <p>Menampilkan <span x-text="filteredEvents.length"></span> dari <span x-text="events.length"></span> kegiatan</p>
        </div>
    </div>
</main>
</div>
@endsection