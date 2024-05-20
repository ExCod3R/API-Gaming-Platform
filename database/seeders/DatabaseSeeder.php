<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
        ]);

        \Spatie\Permission\Models\Role::create(['guard_name' => 'web', 'name' => RoleEnum::SUPER_ADMIN->value]);
        \Spatie\Permission\Models\Role::create(['guard_name' => 'web', 'name' => RoleEnum::CHANNEL_ADMIN->value]);
        \Spatie\Permission\Models\Role::create(['guard_name' => 'api', 'name' => RoleEnum::PLAYER->value]);

        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ])->assignRole(RoleEnum::SUPER_ADMIN->value);

        $channel = \App\Models\Channel::factory()->create([
            'name' => 'Game PVT',
        ]);

        $channel->users()->create([
            'name' => 'Channel Admin',
            'email' => 'channel@admin.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ])->assignRole(RoleEnum::CHANNEL_ADMIN->value);

        $channel->players()->create([
            'name' => 'Sam Player',
            'email' => 'sam@player.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'phone' => '0718505050',
            'country' => 'LK'
        ])->assignRole(RoleEnum::PLAYER->value);

        \App\Models\PackageTerm::create(['name' => 'Daily', 'duration' => '1']);
        \App\Models\PackageTerm::create(['name' => 'Weekly', 'duration' => '7']);
        \App\Models\PackageTerm::create(['name' => 'Monthly', 'duration' => '30']);
        \App\Models\PackageTerm::create(['name' => 'Yearly', 'duration' => '365']);
        \App\Models\PackageTerm::create(['name' => 'Onetime', 'duration' => '0']);

        // \App\Models\User::factory(25)->create();
        // \App\Models\Channel::factory(50)->create();
        // \App\Models\Game::factory(50)->create();
    }
}
