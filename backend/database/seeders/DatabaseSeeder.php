<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\HelpType;
use App\Models\Package;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with the roles and sample content
     * described in the project specification.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        $editor = User::factory()->create([
            'name' => 'Психолог',
            'email' => 'editor@example.com',
            'role' => User::ROLE_EDITOR,
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'role' => User::ROLE_USER,
            'email_verified_at' => now(),
        ]);

        $helpTypes = collect([
            ['name' => 'Индивидуална консултация', 'base_price' => 60],
            ['name' => 'Семейна консултация', 'base_price' => 80],
            ['name' => 'Помощ за жени', 'base_price' => 60],
            ['name' => 'Групова терапия', 'base_price' => 40],
        ])->map(fn (array $ht) => HelpType::create([
            'name' => $ht['name'],
            'slug' => Str::slug($ht['name']).'-'.Str::random(4),
            'base_price' => $ht['base_price'],
        ]));

        Package::create([
            'name' => 'Пакет „5 сесии“',
            'slug' => 'paket-5-sesii-'.Str::random(4),
            'description' => 'Пет индивидуални сесии за 1 месец, спестява 15%.',
            'sessions_count' => 5,
            'price' => 200,
            'validity_days' => 30,
            'is_featured' => true,
        ]);

        Package::create([
            'name' => 'Семеен пакет',
            'slug' => 'semeen-paket-'.Str::random(4),
            'sessions_count' => 4,
            'price' => 280,
            'validity_days' => 60,
        ]);

        // A couple of weeks of free slots for the editor
        for ($day = 1; $day <= 14; $day++) {
            foreach (['10:00', '14:00', '17:00'] as $time) {
                Slot::create([
                    'editor_id' => $editor->id,
                    'date' => now()->addDays($day)->toDateString(),
                    'start_time' => $time,
                    'end_time' => now()->addDays($day)->setTimeFromTimeString($time)->addMinutes(50)->format('H:i'),
                ]);
            }
        }

        Event::create([
            'editor_id' => $editor->id,
            'help_type_id' => $helpTypes[3]->id,
            'title' => 'Кръг на жените',
            'slug' => 'krag-na-zhenite-'.Str::random(4),
            'description' => 'Групова сесия за споделяне и подкрепа.',
            'starts_at' => now()->addDays(10)->setTime(18, 0),
            'price' => 40,
            'capacity' => 12,
        ]);
    }
}
