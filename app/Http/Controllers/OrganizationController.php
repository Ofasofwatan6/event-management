<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with('category')
            ->withCount([
                'events as active_events_count' => fn ($q) => $q->where('end_date', '>=', now()),
            ])
            ->orderBy('org_name')
            ->paginate(50);

        return view('organizations.index', compact('organizations'));
    }

    public function events(Organization $organization)
    {
        $organization->load('category');

        $events = Event::with(['category', 'eventType'])
            ->withCount([
                'registrations as approved_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->where('organization_id', $organization->id)
            ->where('end_date', '>=', now())
            ->latest('start_date')
            ->get();

        return view('organizations.events', compact('organization', 'events'));
    }
}
