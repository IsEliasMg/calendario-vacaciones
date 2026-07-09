<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Admin\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (Admin::query()->where('email', 'admin@calendario.test')->exists()) {
            return;
        }

        Admin::query()->create([
            'name' => 'Administradora',
            'email' => 'admin@calendario.test',
            'password' => 'password',
        ]);
    }
}
