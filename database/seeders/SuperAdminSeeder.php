<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the super admin account.
     * Safe to re-run — uses updateOrCreate.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'abhay-byte'],
            [
                'name'     => 'Abhay',
                'email'    => 'abhay@phonefinderhub.local',
                'username' => 'abhay-byte',
                'password' => Hash::make('@NMy)+=73r~@3Q}u,eNodvBR%aaB]QZYQ~LN+kQ0u4inP8}+7!!Yv#FJL33N?}_C-=_:2+)CTTfXV_Z6hZ6w#?8!.>d19tMKj,?i'),
                'role'     => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✓ Super admin [abhay-byte] seeded successfully.');
    }
}
