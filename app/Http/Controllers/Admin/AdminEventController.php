<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ScopesOrganizationEvents;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminEventController extends Controller
{
    use ScopesOrganizationEvents;

    public function dashboard()
    {
        $org = $this->myOrganization();

        $events = $this->organizationEventsQuery()
            ->with(['category', 'eventType'])
            ->withCount([
                'registrations',
                'registrations as approved_count' => fn ($q) => $q->where('status', 'approved'),
                'registrations as pending_count' => fn ($q) => $q->where('status', 'pending'),
            ])
            ->latest()
            ->get();

        $stats = [
            'total_events' => $events->count(),
            'total_registered' => $events->sum('registrations_count'),
            'upcoming_events' => $events->where('start_date', '>=', now())->count(),
        ];

        $eventsJson = $events->map(fn ($e) => $this->toAlpineEvent($e));

        return view('admin.dashboard', compact('org', 'events', 'stats', 'eventsJson'));
    }

    public function index()
    {
        $org = $this->myOrganization();

        $events = $this->organizationEventsQuery()
            ->with(['category', 'eventType'])
            ->withCount([
                'registrations',
                'registrations as approved_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->latest()
            ->get();

        $categories = EventCategory::orderBy('group_type')->orderBy('name')->get();
        $eventTypes = EventType::orderBy('name')->get();
        $eventsJson = $events->map(fn ($e) => $this->toAlpineManageEvent($e));
        $categoryOptions = $categories->pluck('name')->unique()->values();

        return view('admin.events.index', compact('events', 'categories', 'eventTypes', 'org', 'eventsJson', 'categoryOptions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateEventPayload($request);

        $org = $this->myOrganization();
        $category = $this->resolveCategory($validated['cat']);
        $eventType = $this->resolveEventType($validated['type']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('events', 'public');
            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                $imagePath = $storedPath;
            }
        }

        Event::create([
            'organization_id' => $org->id,
            'event_category_id' => $category->id,
            'event_type_id' => $eventType->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'location' => $validated['location'] ?? null,
            'quota' => $validated['quota'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'type' => ($validated['price'] ?? 0) > 0 ? 'paid' : 'free',
            'price' => ($validated['price'] ?? 0) > 0 ? $validated['price'] : null,
        ]);

        return back()->with('success', 'Event berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $event = Event::where('organization_id', $this->myOrganization()->id)->findOrFail($id);

        $validated = $this->validateEventPayload($request, $event);

        $category = $this->resolveCategory($validated['cat']);
        $eventType = $this->resolveEventType($validated['type']);

        $payload = [
            'event_category_id' => $category->id,
            'event_type_id' => $eventType->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? $event->description,
            'location' => $validated['location'] ?? $event->location,
            'quota' => $validated['quota'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'type' => ($validated['price'] ?? 0) > 0 ? 'paid' : 'free',
            'price' => ($validated['price'] ?? 0) > 0 ? $validated['price'] : null,
        ];

        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('events', 'public');

            if (! $storedPath || ! Storage::disk('public')->exists($storedPath)) {
                return back()->withInput()->with('error', 'Gagal menyimpan gambar event.');
            }

            $old = \App\Support\StorageImage::normalize($event->image);
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $payload['image'] = $storedPath;
        }

        $event->update($payload);

        return back()->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $event = Event::withTrashed()
            ->where('organization_id', $this->myOrganization()->id)
            ->findOrFail($id);
        $event->forceDelete();

        return back()->with('success', 'Event berhasil dihapus!');
    }

    private function organizationEventsQuery()
    {
        return Event::where('organization_id', $this->myOrganization()->id)
            ->whereNull('deleted_at');
    }

    private function validateEventPayload(Request $request, ?Event $event = null): array
    {
        $minQuota = 1;
        if ($event) {
            $minQuota = max(1, $event->registrations()->where('status', 'approved')->count());
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cat' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'quota' => "required|integer|min:{$minQuota}",
            'price' => 'nullable|numeric|min:0',
            'location' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.]+,\s*[\pL\s\-\.]+$/u'],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'image' => 'nullable|image|max:5120',
        ], [
            'required' => 'Wajib Diisi',
            'title.required' => 'Judul kegiatan wajib diisi.',
            'cat.required' => 'Kategori wajib dipilih.',
            'type.required' => 'Tipe pelaksanaan wajib dipilih.',
            'quota.required' => 'Kuota peserta wajib diisi.',
            'quota.integer' => 'Kuota harus berupa angka.',
            'quota.min' => 'Kuota tidak boleh kurang dari jumlah peserta terdaftar.',
            'price.numeric' => 'Biaya harus berupa angka.',
            'price.min' => 'Biaya tidak boleh negatif.',
            'location.regex' => 'Format lokasi: Kota, Negara (contoh: Jakarta, Indonesia).',
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar tidak boleh melebihi 5120 KB.',
        ]);

        $validated['start_date'] = $validated['start_date'] ?? now()->addDays(7)->format('Y-m-d H:i:s');
        $validated['end_date'] = $validated['end_date'] ?? now()->addDays(14)->format('Y-m-d H:i:s');

        return $validated;
    }

    private function resolveCategory(string $cat): EventCategory
    {
        return EventCategory::where('name', $cat)->first()
            ?? EventCategory::where('group_type', $cat)->firstOrFail();
    }

    private function resolveEventType(string $type): EventType
    {
        return EventType::where('name', $type)->firstOrFail();
    }

    private function toAlpineEvent(Event $e): array
    {
        $approved = $e->approved_count ?? 0;

        return [
            'id' => $e->id,
            'title' => $e->title,
            'org' => $this->myOrganization()->org_name,
            'registered' => $approved,
            'quota' => $e->quota,
            'type' => $e->eventType->name ?? 'Onsite',
            'date' => optional($e->start_date)->format('d M Y'),
            'status' => $this->eventStatusLabel($e, $approved),
        ];
    }

    private function toAlpineManageEvent(Event $e): array
    {
        $approved = $e->approved_count ?? 0;

        return [
            'id' => $e->id,
            'title' => $e->title,
            'cat' => $e->category->name ?? $e->category->group_type ?? 'Volunteer',
            'type' => $e->eventType->name ?? 'Onsite',
            'description' => $e->description ?? '',
            'registered' => $approved,
            'quota' => $e->quota,
            'price' => $e->isFree() ? 0 : (float) ($e->price ?? 0),
            'location' => $e->location ?? '',
            'start_date' => optional($e->start_date)->format('Y-m-d\TH:i'),
            'end_date' => optional($e->end_date)->format('Y-m-d\TH:i'),
        ];
    }

    private function eventStatusLabel(Event $e, int $approved): string
    {
        if ($approved >= $e->quota) {
            return 'Penuh';
        }

        if ($e->start_date && $e->start_date->isPast() && $e->end_date && $e->end_date->isFuture()) {
            return 'Berjalan';
        }

        return 'Mendatang';
    }
}
