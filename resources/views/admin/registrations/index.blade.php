@extends('layouts.admin')
@section('title', 'Manajemen Pendaftaran | VolunteerHub')

@section('content')
<div x-data="{
    searchQuery: '', selectedStatus: 'All',
    showProof: false, currentImg: '',
    registrations: @js($registrationsJson),
    get filteredRegs() {
        return this.registrations.filter(r => {
            const q = this.searchQuery.toLowerCase();
            const matchSearch = !q || r.name.toLowerCase().includes(q) || String(r.participantId).includes(q)
                || String(r.paymentId || '').includes(q) || (r.eventTitle || '').toLowerCase().includes(q);
            const matchStatus = this.selectedStatus === 'All' || r.status === this.selectedStatus;
            return matchSearch && matchStatus;
        });
    },
    updateStatus(id, action) {
        const form = document.getElementById('registration-action-' + action + '-' + id);
        if (form) form.submit();
    }
}">

<main class="max-w-[1200px] mx-auto w-full px-4 md:px-6 py-10 flex-grow">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Manajemen Pendaftaran</h1>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <select x-model="selectedStatus" class="rounded-xl border-zinc-200 text-sm focus:ring-[#2F7F79] w-full sm:w-48">
                <option value="All">Semua Status</option>
                <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                <option value="Pending">Pending</option>
                <option value="Diterima">Diterima</option>
                <option value="Ditolak">Ditolak</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>
            <input type="text" x-model="searchQuery" placeholder="Cari nama, ID, atau nama kegiatan..."
                   class="rounded-xl border-zinc-200 text-sm focus:ring-[#2F7F79] border px-4 py-2 flex-grow"/>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-zinc-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[1100px]">
                <thead class="bg-slate-50 border-b border-zinc-100 text-xs font-black uppercase text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-6 py-4">ID Peserta</th>
                        <th class="px-6 py-4">ID Pembayaran</th>
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-6 py-4">Nama Kegiatan</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Bukti</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50">
                    <template x-for="reg in filteredRegs" :key="reg.id">
                        <tr class="hover:bg-slate-50 transition-all">
                            <td class="px-6 py-5 font-mono text-xs font-bold text-[#2F7F79]" x-text="'P-' + reg.participantId"></td>
                            <td class="px-6 py-5 font-mono text-xs" x-text="reg.paymentId ? 'PAY-' + reg.paymentId : '-'"></td>
                            <td class="px-6 py-5 font-bold" x-text="reg.name"></td>
                            <td class="px-6 py-5 text-slate-500 text-sm" x-text="reg.eventTitle"></td>
                            <td class="px-6 py-5 text-sm font-black text-[#2F7F79]" x-text="reg.amount"></td>
                            <td class="px-6 py-5 text-sm" x-text="reg.method"></td>
                            <td class="px-6 py-5">
                                <button type="button" @click="if(reg.proofImg) { currentImg = reg.proofImg; showProof = true; }"
                                        :disabled="!reg.proofImg"
                                        :class="!reg.proofImg ? 'opacity-40 cursor-not-allowed' : 'hover:underline'"
                                        class="text-xs font-bold text-blue-600 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">image</span> Lihat
                                </button>
                            </td>
                            <td class="px-6 py-5">
                                <span :class="{
                                    'bg-orange-100 text-orange-600': reg.status.includes('Menunggu') || reg.status === 'Pending',
                                    'bg-green-100 text-green-600': reg.status === 'Diterima',
                                    'bg-red-100 text-red-600': reg.status === 'Ditolak',
                                    'bg-slate-100 text-slate-600': reg.status === 'Dibatalkan'
                                }" class="px-3 py-1 text-[10px] font-black uppercase rounded-lg" x-text="reg.status"></span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center gap-2">
                                    <form :id="'registration-action-approve-' + reg.id" method="POST" :action="'{{ url('/admin/registrations') }}/' + reg.id + '/approve'" class="inline">
                                        @csrf
                                        <button type="button" @click="updateStatus(reg.id, 'approve')"
                                            :disabled="reg.isFinal"
                                            class="px-3 py-1.5 bg-[#2F7F79] text-white rounded-lg text-xs font-bold disabled:opacity-40 disabled:cursor-not-allowed">Terima</button>
                                    </form>
                                    <form :id="'registration-action-reject-' + reg.id" method="POST" :action="'{{ url('/admin/registrations') }}/' + reg.id + '/reject'" class="inline">
                                        @csrf
                                        <button type="button" @click="updateStatus(reg.id, 'reject')"
                                            :disabled="reg.isFinal"
                                            class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold disabled:opacity-40 disabled:cursor-not-allowed">Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredRegs.length === 0">
                        <tr><td colspan="9" class="text-center py-12 text-slate-400 italic text-sm">Tidak ada data pendaftaran.</td></tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showProof" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showProof = false"></div>
        <div class="relative bg-white rounded-[40px] p-5 shadow-2xl max-w-sm w-full">
            <img :src="currentImg" class="w-full h-auto max-h-[60vh] object-contain rounded-3xl" alt="Bukti"/>
            <button @click="showProof = false" class="w-full mt-4 py-3 bg-[#2F7F79] text-white rounded-2xl font-black">Tutup</button>
        </div>
    </div>
</main>
</div>
@endsection
