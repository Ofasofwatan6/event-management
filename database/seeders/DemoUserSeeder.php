<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventType;
use App\Models\Organization;
use App\Models\OrganizationCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $lingkungan = OrganizationCategory::where('name', 'Lingkungan')->first();

        if (! $lingkungan) {
            $this->command?->warn('DemoUserSeeder: jalankan LookupSeeder terlebih dahulu.');

            return;
        }

        $greenEarth = User::updateOrCreate(
            ['email' => 'greenearth@volunteerhub.test'],
            [
                'name' => 'GreenEarth Foundation Admin',
                'password' => 'Password123!',
                'role' => 'organization',
            ]
        );

        $organization = Organization::updateOrCreate(
            ['user_id' => $greenEarth->id],
            [
                'organization_category_id' => $lingkungan->id,
                'org_name' => 'GreenEarth Foundation',
                'description' => 'Organisasi lingkungan untuk kegiatan relawan dan edukasi berkelanjutan.',
                'phone' => '081234567890',
                'email' => 'greenearth@volunteerhub.test',
                'address' => 'Jakarta, Indonesia',
            ]
        );

        User::updateOrCreate(
            ['email' => 'volunteer@volunteerhub.test'],
            [
                'name' => 'Demo Volunteer',
                'password' => 'Password123!',
                'role' => 'user',
            ]
        );

        $volunteerCategory = EventCategory::where('group_type', 'Volunteer')->first()
            ?? EventCategory::first();
        $onsiteType = EventType::where('name', 'Onsite')->first()
            ?? EventType::first();

        if ($organization && $volunteerCategory && $onsiteType) {
            Event::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'title' => 'Penanaman Mangrove Bersama',
                ],
                [
                    'event_category_id' => $volunteerCategory->id,
                    'event_type_id' => $onsiteType->id,
                    'description' => 'Aksi relawan penanaman mangrove untuk restorasi pesisir dan edukasi lingkungan.',
                    'location' => 'Pantai Utara, Jakarta',
                    'quota' => 50,
                    'start_date' => now()->addDays(14),
                    'end_date' => now()->addDays(21),
                    'type' => 'free',
                    'price' => null,
                ]
            );
        }

        $this->repairInvalidPasswordHashes();
    }

    private function repairInvalidPasswordHashes(): void
    {
        User::query()->each(function (User $user) {
            $stored = $user->getRawOriginal('password');

            if ($stored && Hash::isHashed($stored) && strlen($stored) >= 60) {
                return;
            }

            $user->forceFill(['password' => 'password'])->save();
        });
    }
}
