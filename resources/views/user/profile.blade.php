@extends('layouts.app')

@section('title', 'Profil & Riwayat - VolunteerHub')

@push('styles')
<style>
    #editModal.show { display: flex; }
    .fill-1 { font-variation-settings: 'FILL' 1; }
</style>
@endpush

@section('content')

<div id="editModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white p-6 md:p-8 rounded-2xl max-w-lg w-full shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-[#263238]">Ubah Profil Publik</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-lg border border-gray-200 text-sm p-3 focus:ring-[#2F7F79] focus:border-[#2F7F79] outline-none @error('name') border-red-500 @enderror"/>
                <x-field-error field="name"/>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nama Panggilan</label>
                <input type="text" name="nickname" value="{{ old('nickname', $user->nickname) }}"
                       class="w-full rounded-lg border border-gray-200 text-sm p-3 focus:ring-[#2F7F79] outline-none @error('nickname') border-red-500 @enderror"/>
                <x-field-error field="nickname"/>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Status Relawan</label>
                <input type="text" name="volunteer_status" value="{{ old('volunteer_status', $user->volunteer_status ?? 'Relawan Aktif') }}"
                       class="w-full rounded-lg border border-gray-200 text-sm p-3 focus:ring-[#2F7F79] outline-none @error('volunteer_status') border-red-500 @enderror"/>
                <x-field-error field="volunteer_status"/>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Kota</label>
                <input type="text" name="city" value="{{ old('city', $user->city) }}"
                       class="w-full rounded-lg border border-gray-200 text-sm p-3 focus:ring-[#2F7F79] outline-none @error('city') border-red-500 @enderror"/>
                <x-field-error field="city"/>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full rounded-lg border border-gray-200 text-sm p-3 focus:ring-[#2F7F79] outline-none @error('email') border-red-500 @enderror"/>
                <x-field-error field="email"/>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" pattern="\d{10,13}"
                       class="w-full rounded-lg border border-gray-200 text-sm p-3 focus:ring-[#2F7F79] outline-none @error('phone') border-red-500 @enderror"/>
                <x-field-error field="phone"/>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Foto Profil</label>
                <input type="file" name="profile_image" accept="image/png,image/jpeg,image/jpg"
                       class="w-full rounded-lg border border-gray-200 text-sm p-2 text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#2F7F79]/10 file:text-[#2F7F79]"/>
                <x-field-error field="profile_image"/>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-500 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#2F7F79] text-white text-sm font-bold hover:opacity-90 shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<main class="flex-1 flex justify-center py-8 px-4 lg:px-20 bg-[#f6f8f6] min-h-[calc(100vh-200px)]">
    <div class="max-w-[1200px] w-full flex flex-col md:flex-row gap-8">

        <aside class="w-full md:w-64 flex flex-col gap-6 shrink-0">
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-6">
                <div class="flex gap-3 items-center px-2">
                    <x-avatar
                        :name="$user->name"
                        :image-url="$user->profile_image_url"
                        :has-image="$user->has_profile_image"
                        :seed="$user->email"
                        class="size-12 rounded-full shadow-sm"
                        text-class="text-sm font-bold text-white"
                    />
                    <div class="flex flex-col min-w-0">
                        <h1 class="text-[#263238] text-base font-bold leading-none truncate">
                            {{ $user->nickname ?: $user->name }}
                        </h1>
                        <p class="text-[#2F7F79] text-xs font-medium mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">verified</span>
                            <span>{{ $user->volunteer_status ?? 'Relawan Aktif' }}</span>
                        </p>
                    </div>
                </div>

                <nav class="flex flex-col gap-1">
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Aktivitas</p>
                    <a href="{{ route('user.past.events') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[#263238] hover:bg-gray-100 transition-all">
                        <span class="material-symbols-outlined text-[22px]">history</span>
                        <span class="text-sm font-medium">Aktivitas Saya</span>
                    </a>
                    <a href="{{ route('user.payment.history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[#263238] hover:bg-gray-100 transition-all">
                        <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                        <span class="text-sm font-medium">Riwayat Pembayaran</span>
                    </a>
                    <a href="{{ route('refund.status') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[#263238] hover:bg-gray-100 transition-all">
                        <span class="material-symbols-outlined text-[22px]">receipt</span>
                        <span class="text-sm font-medium">Status Pengembalian</span>
                    </a>

                    <div class="my-3 border-t border-gray-100"></div>
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Akun</p>
                    <a href="{{ route('forgot-password') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[#263238] hover:bg-gray-100 transition-all">
                        <span class="material-symbols-outlined text-[22px]">security</span>
                        <span class="text-sm font-medium">Kata Sandi</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar?')"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[22px]">logout</span>
                            <span class="text-sm font-medium">Keluar</span>
                        </button>
                    </form>
                </nav>
            </div>

            <button type="button" onclick="openModal()"
                    class="w-full flex items-center justify-center gap-2 rounded-xl h-11 bg-[#2F7F79] text-white text-sm font-bold shadow-lg hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-sm">edit</span>
                Edit Profil Publik
            </button>
        </aside>

        <div class="flex-1 flex flex-col gap-6 min-w-0">
            <div class="flex flex-wrap gap-2 items-center text-sm font-medium">
                <a class="text-gray-500 hover:text-[#2F7F79] transition-colors" href="{{ route('home') }}">Beranda</a>
                <span class="text-gray-400">/</span>
                <span class="text-[#263238]">Profil & Riwayat</span>
            </div>

            <section class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                    <x-avatar
                        :name="$user->name"
                        :image-url="$user->profile_image_url"
                        :has-image="$user->has_profile_image"
                        :seed="$user->email"
                        class="size-24 rounded-full border-4 border-[#f6f8f6] shadow-md"
                        text-class="text-2xl font-bold text-white"
                    />
                    <div>
                        <h2 class="text-2xl font-black text-[#263238]">{{ $user->name }}</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
                        @if($user->city)
                            <p class="text-gray-400 text-sm mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">location_on</span>{{ $user->city }}
                            </p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">Bergabung {{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>

            </section>
        </div>
    </div>
</main>

@endsection

@push('scripts')
<script>
    function openModal() { document.getElementById('editModal').classList.add('show'); }
    function closeModal() { document.getElementById('editModal').classList.remove('show'); }
    @if($errors->any() || session('success')) openModal(); @endif
    @if(session('success')) alert(@json(session('success'))); @endif
</script>
@endpush
