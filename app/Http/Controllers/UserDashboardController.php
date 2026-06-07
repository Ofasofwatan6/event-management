<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserDashboardController extends Controller
{
    public function pastEvents()
    {
        $registrations = EventRegistration::with([
            'event.organization',
            'event.category',
            'event.eventType',
            'participant',
        ])
            ->where('status', 'approved')
            ->whereHas('event', fn ($q) => $q->where('end_date', '<', now()))
            ->whereIn(
                'participant_id',
                auth()->user()->participants()->pluck('id')
            )
            ->latest()
            ->get();

        return view('user.past_events', compact('registrations'));
    }

    public function profile()
    {
        $user = auth()->user();
        $participantIds = $user->participants()->pluck('id');

        $stats = [
            'joined_events' => EventRegistration::whereIn('participant_id', $participantIds)
                ->where('status', 'approved')
                ->count(),
            'registration_count' => EventRegistration::whereIn('participant_id', $participantIds)->count(),
        ];

        return view('user.profile', compact('user', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^\d{10,13}$/'],
            'volunteer_status' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:png,jpeg,jpg|max:5120',
        ], [
            'required' => 'Wajib Diisi',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'phone.regex' => 'Nomor telepon harus 10–13 digit angka.',
            'profile_image.image' => 'File harus berupa gambar.',
            'profile_image.max' => 'Ukuran foto tidak boleh melebihi 5120 KB.',
        ]);

        if ($request->hasFile('profile_image')) {
            $storedPath = $request->file('profile_image')
                ->store('profile_images', 'public');

            // Some filesystem errors can fail silently depending on disk config.
            // Only persist the DB path when the file is actually there.
            if (! $storedPath || ! Storage::disk('public')->exists($storedPath)) {
                return back()->withInput()->with('error', 'Gagal menyimpan foto profil.');
            }

            $validated['profile_image'] = $storedPath;
        } else {
            unset($validated['profile_image']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
