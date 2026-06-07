<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Organization;
use App\Models\Payment;

trait ScopesOrganizationEvents
{
    protected function myOrganization(): Organization
    {
        return auth()->user()->organization;
    }

    protected function organizationEventIds(): array
    {
        return Event::where('organization_id', $this->myOrganization()->id)->pluck('id')->all();
    }

    protected function findOrganizationRegistration(int $id): EventRegistration
    {
        return EventRegistration::whereIn('event_id', $this->organizationEventIds())
            ->findOrFail($id);
    }

    protected function findOrganizationPayment(int $id): Payment
    {
        return Payment::whereHas('eventRegistration', function ($q) {
            $q->whereIn('event_id', $this->organizationEventIds());
        })->findOrFail($id);
    }
}
