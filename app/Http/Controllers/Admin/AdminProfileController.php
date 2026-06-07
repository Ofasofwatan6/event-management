<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ScopesOrganizationEvents;
use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use App\Support\StorageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    use ScopesOrganizationEvents;

    public function show()
    {
        $org = $this->myOrganization()->load('category', 'user');
        $eventIds = $this->organizationEventIds();

        $stats = [
            'managed_activities' => $org->events()->count(),
            'processed_volunteers' => EventRegistration::whereIn('event_id', $eventIds)
                ->where('status', 'approved')
                ->distinct('participant_id')
                ->count('participant_id'),
            'pending_registrations' => EventRegistration::whereIn('event_id', $eventIds)
                ->where('status', 'pending')
                ->count(),
        ];

        return view('admin.profile', compact('org', 'stats'));
    }

    public function update(Request $request)
    {
        $org = $this->myOrganization();

        $validated = $request->validate([
            'org_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => ['nullable', 'regex:/^\d{11,13}$/'],
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:png,jpeg,jpg|max:5120',
        ], [
            'required' => 'Wajib Diisi',
            'org_name.required' => 'Nama organisasi wajib diisi.',
            'phone.regex' => 'Nomor telepon harus 11–13 digit angka.',
            'email.email' => 'Format email tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar tidak boleh melebihi 5120 KB.',
        ]);

        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('organizations', 'public');

            if (! $storedPath || ! Storage::disk('public')->exists($storedPath)) {
                return back()->withInput()->with('error', 'Gagal menyimpan gambar organisasi.');
            }

            // Only delete the old file after the new one is safely stored.
            $old = StorageImage::normalize($org->image);
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $validated['image'] = $storedPath;
        } else {
            unset($validated['image']);
        }

        $org->update($validated);

        if ($request->filled('name')) {
            $org->user->update(['name' => $request->name]);
        }

        return back()->with('success', 'Profil organisasi berhasil diperbarui.');
    }
}
