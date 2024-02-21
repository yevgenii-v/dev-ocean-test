<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => RoleEnum::Administrator->name,
        ]);

        Role::create([
            'name' => RoleEnum::User->name,
        ]);
    }
}
