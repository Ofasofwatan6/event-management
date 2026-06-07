@extends('layouts.admin')
@section('title', 'Refund Management | VolunteerHub')

@section('content')
<div x-data="{
    searchQuery: '', statusFilter: 'Semua',
    showProof: false, currentImg: '',
    showApprove: false, approveId: null,
    previewUrl: null,
    previewFile: null,
    refunds: @js($refundsJson),
    get filteredRefunds() {
        return this.refunds.filter(r => {
            const matchStatus = this.statusFilter === 'Semua' || r.status === this.statusFilter;
            const q = this.searchQuery.toLowerCase();
            const matchSearch = !q || r.name.toLowerCase().includes(q) || (r.eventTitle || '').toLowerCase().includes(q);
            return matchStatus && matchSearch;
        });
    },
    onFileSelect(e) {
        const file = e.target.files[0];
        if (!file) { this.previewUrl = null; this.previewFile = null; return; }
        this.previewFile = file;
        const reader = new FileReader();
        reader.onload = (ev) => { this.previewUrl = ev.target.result; };
        reader.readAsDataURL(file);
    }
}">

<main class="max-w-[1200px] mx-auto w-full px-4 md:px-6 py-10 flex-grow">
    <h1 class="text-2xl md:text-3xl font-black tracking-tight mb-8">Refund Management</h1>

    <div class="bg-white p-6 rounded-3xl border border-zinc-100 shadow-sm mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <select x-model="statusFilter" class="rounded-xl border-zinc-200 text-sm focus:ring-[#2F7F79] w-full md:w-48">
            <option value="Semua">Semua Status</option>
            <option value="Menunggu">Menunggu</option>
            <option value="Disetujui">Disetujui</option>
            <option value="Ditolak">Ditolak</option>
        </select>
        <input type="text" x-model="searchQuery" placeholder="Cari nama atau kegiatan..."
               class="rounded-xl border-zinc-200 text-sm focus:ring-[#2F7F79] w-full md:w-64"/>
    </div>

    <div class="bg-white rounded-3xl border border-zinc-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[900px]">
                <thead class="bg-slate-50 border-b text-xs font-black uppercase text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-8 py-4">Peserta</th>
                        <th class="px-8 py-4">Kegiatan</th>
                        <th class="px-8 py-4">Jumlah</th>
                        <th class="px-8 py-4">Rekening</th>
                        <th class="px-8 py-4">Bukti Transfer</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50">
                    <template x-for="ref in filteredRefunds" :key="ref.id">
                        <tr class="hover:bg-slate-50">
                            <td class="px-8 py-6 font-bold" x-text="ref.name"></td>
                            <td class="px-8 py-6 text-sm" x-text="ref.eventTitle"></td>
                            <td class="px-8 py-6 font-black text-[#2F7F79]" x-text="ref.amount"></td>
                            <td class="px-8 py-6 text-xs text-slate-500" x-text="ref.bank"></td>
                            <td class="px-8 py-6">
                                <template x-if="ref.transferProof">
                                    <button type="button" @click="currentImg = ref.transferProof; showProof = true"
                                            class="text-xs font-bold text-blue-600 hover:underline">Lihat Bukti Admin</button>
                                </template>
                                <template x-if="!ref.transferProof">
                                    <span class="text-xs text-gray-400">—</span>
                                </template>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 text-[10px] font-black uppercase rounded-lg"
                                      :class="{
                                          'bg-yellow-100 text-yellow-700': ref.status === 'Menunggu',
                                          'bg-green-100 text-green-600': ref.status === 'Disetujui',
                                          'bg-red-100 text-red-600': ref.status === 'Ditolak'
                                      }" x-text="ref.status"></span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-center gap-2" x-show="ref.status === 'Menunggu'">
                                    <button type="button" @click="approveId = ref.id; previewUrl = null; showApprove = true"
                                            class="px-3 py-1.5 bg-[#2F7F79] text-white rounded-lg text-xs font-black">Setujui</button>
                                    <form :id="'refund-reject-' + ref.id" method="POST" :action="'{{ url('/admin/refund') }}/' + ref.id + '/reject'" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-black">Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showProof" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60" @click="showProof = false"></div>
        <div class="relative bg-white rounded-3xl p-5 max-w-lg w-full">
            <img :src="currentImg" class="w-full max-h-[70vh] object-contain rounded-2xl border" alt="Bukti transfer"/>
            <button @click="showProof = false" class="w-full mt-4 py-3 bg-[#2F7F79] text-white rounded-2xl font-black">Tutup</button>
        </div>
    </div>

    <div x-show="showApprove" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60" @click="showApprove = false"></div>
        <div class="relative bg-white rounded-3xl p-6 max-w-md w-full max-h-[90vh] overflow-y-auto">
            <h3 class="font-black text-lg mb-4">Setujui Refund</h3>
            <form :action="'{{ url('/admin/refund') }}/' + approveId + '/approve'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-bold block mb-2">Unggah Bukti Transfer <span class="text-red-500">*</span></label>
                    <input type="file" name="transfer_proof" required accept="image/png,image/jpeg,image/jpg"
                           @change="onFileSelect($event)"
                           class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#2F7F79]/10 file:text-[#2F7F79] file:font-bold"/>
                    <template x-if="previewUrl">
                        <div class="mt-3 border rounded-xl p-2 bg-slate-50">
                            <p class="text-xs font-bold text-gray-500 mb-2">Pratinjau:</p>
                            <img :src="previewUrl" class="w-full max-h-48 object-contain rounded-lg" alt="Preview"/>
                        </div>
                    </template>
                </div>
                <div>
                    <label class="text-sm font-bold">Catatan (opsional)</label>
                    <textarea name="note" rows="2" class="w-full mt-1 rounded-xl border p-2 text-sm"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showApprove = false" class="flex-1 py-3 bg-slate-100 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-[#2F7F79] text-white rounded-xl font-black">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</main>
</div>
@endsection
