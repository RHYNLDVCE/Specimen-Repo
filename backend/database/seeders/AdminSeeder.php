<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin from environment variables or defaults
        $superAdminEmail = strtolower(env('ADMIN_SEED_EMAIL', 'rhayan@gmail.com'));
        $superAdminName = env('ADMIN_SEED_NAME', 'Rhayan Lodovice');
        $superAdminPassword = env('ADMIN_SEED_PASSWORD', 'rhynlodo');

        $oldAdmin = Admin::where('email', 'admin@specimenrepo.local')->first();
        if ($oldAdmin) {
            $oldAdmin->update([
                'name' => $superAdminName,
                'email' => $superAdminEmail,
                'password' => Hash::make($superAdminPassword),
                'role' => 'super_admin',
                'status' => 'active',
            ]);
        } else {
            Admin::updateOrCreate(
                ['email' => $superAdminEmail],
                [
                    'name' => $superAdminName,
                    'password' => Hash::make($superAdminPassword),
                    'role' => 'super_admin',
                    'status' => 'active',
                ]
            );
        }

        // 2. Regular Admin (Curator)
        Admin::updateOrCreate(
            ['email' => 'curator@specimenrepo.local'],
            [
                'name' => 'Specimen Curator',
                'password' => Hash::make('CuratorPass123!'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 3. Researcher Account
        Admin::updateOrCreate(
            ['email' => 'researcher@specimenrepo.local'],
            [
                'name' => 'Resident Researcher',
                'password' => Hash::make('ResearcherPass123!'),
                'role' => 'researcher',
                'status' => 'active',
            ]
        );
    }
}
