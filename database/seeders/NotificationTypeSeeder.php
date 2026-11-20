<?php

namespace Database\Seeders;

use App\Models\NotificationType;
use Illuminate\Database\Seeder;

class NotificationTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_filter(NotificationType::definitions(), fn (array $type): bool => in_array($type['slug'], [
            NotificationType::ENROLLMENT_STATUS,
            NotificationType::PLACEMENT_STATUS,
            NotificationType::PLACEMENT_TEST_RECOMMENDED,
            NotificationType::DOCUMENT_STATUS_UPDATED,
        ], true)) as $type) {
            NotificationType::query()->updateOrCreate(
                ['slug' => $type['slug']],
                ['name' => $type['name'], 'sort_order' => $type['sort_order']],
            );
        }

        NotificationType::clearOptionsCache();
    }
}
