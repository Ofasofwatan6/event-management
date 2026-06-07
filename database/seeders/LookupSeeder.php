<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use App\Models\EventType;
use App\Models\OrganizationCategory;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        $orgCategories = [
            ['name' => 'Lingkungan', 'emoji' => '🌎', 'description' => 'Organisasi lingkungan dan keberlanjutan'],
            ['name' => 'Pendidikan', 'emoji' => '🎓', 'description' => 'Organisasi pendidikan dan literasi'],
            ['name' => 'Komunitas', 'emoji' => '🏃', 'description' => 'Komunitas dan kegiatan sosial'],
            ['name' => 'Bantuan Pangan', 'emoji' => '🍎', 'description' => 'Distribusi pangan dan sosial'],
            ['name' => 'Kepemimpinan', 'emoji' => '⛰️', 'description' => 'Pengembangan kepemimpinan'],
        ];

        foreach ($orgCategories as $cat) {
            OrganizationCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        $eventCategories = [
            ['name' => 'Soft Skill', 'group_type' => 'Self Development'],
            ['name' => 'Hard Skill', 'group_type' => 'Self Development'],
            ['name' => 'Career', 'group_type' => 'Self Development'],
            ['name' => 'Creative', 'group_type' => 'Self Development'],
            ['name' => 'Volunteer', 'group_type' => 'Volunteer'],
            ['name' => 'Community', 'group_type' => 'Community'],
        ];

        foreach ($eventCategories as $cat) {
            EventCategory::firstOrCreate(
                ['name' => $cat['name'], 'group_type' => $cat['group_type']],
                ['description' => null]
            );
        }

        foreach (['Online', 'Onsite', 'Hybrid'] as $typeName) {
            EventType::firstOrCreate(['name' => $typeName], ['description' => null]);
        }

        $paymentMethods = [
            ['name' => 'BCA', 'type' => 'bank', 'account_name' => 'VolunteerHub', 'account_number' => '882900124451', 'instructions' => 'Transfer ke rekening BCA', 'is_active' => true],
            ['name' => 'Mandiri', 'type' => 'bank', 'account_name' => 'VolunteerHub', 'account_number' => '1234567890', 'instructions' => 'Transfer ke rekening Mandiri', 'is_active' => true],
            ['name' => 'GoPay', 'type' => 'ewallet', 'account_name' => 'VolunteerHub', 'account_number' => '081234567890', 'instructions' => 'Transfer GoPay', 'is_active' => true],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(['name' => $method['name']], $method);
        }
    }
}
