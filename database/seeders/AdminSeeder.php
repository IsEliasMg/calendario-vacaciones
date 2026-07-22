<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Admin\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private const ALLOWED_EMAILS = [
        'administrador@laboratoriocoahuila.com',
        'direcciongeneral@laboratoriocoahuila.com',
    ];

    public function run(): void
    {
        $admins = [
            [
                'name' => 'Administrador',
                'email' => 'administrador@laboratoriocoahuila.com',
                'password' => '@administracionrh',
            ],
            [
                'name' => 'Direccion General',
                'email' => 'direcciongeneral@laboratoriocoahuila.com',
                'password' => '@administracionrh',
            ],
        ];

        Admin::query()
            ->whereNotIn('email', self::ALLOWED_EMAILS)
            ->delete();

        foreach ($admins as $admin) {
            Admin::query()->updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => $admin['password'],
                ]
            );
        }
    }
}
