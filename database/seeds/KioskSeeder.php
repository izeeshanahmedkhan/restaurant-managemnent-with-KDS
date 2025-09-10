<?php

use Illuminate\Database\Seeder;

class KioskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first branch
        $branch = \App\Model\Branch::first();
        
        if (!$branch) {
            $this->command->error('No branch found. Please create a branch first.');
            return;
        }

        // Create a sample kiosk
        $kiosk = \App\Models\Kiosk::create([
            'name' => 'Main Kiosk',
            'branch_id' => $branch->id,
            'device_id' => 'KIOSK-001',
            'is_active' => 1
        ]);

        // Create a kiosk user
        $kioskUser = \App\Models\KioskUser::create([
            'kiosk_id' => $kiosk->id,
            'f_name' => 'Kiosk',
            'l_name' => 'User',
            'email' => 'kiosk@test.com',
            'password' => bcrypt('123456'),
            'phone' => '0123456789',
            'is_active' => 1
        ]);

        $this->command->info('Kiosk created: ' . $kiosk->name);
        $this->command->info('Kiosk User created: ' . $kioskUser->email);
        $this->command->info('Branch: ' . $branch->name);
        $this->command->info('Login credentials: kiosk@test.com / 123456');
    }
}
