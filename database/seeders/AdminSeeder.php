<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        $admin = User::where('email', 'admin@portal.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@portal.com',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_verified' => true,
                'is_approved' => true,
                'status' => 'approved',
                'admin_approved_at' => now(),
            ]);
            
            echo "✅ Admin user created: admin@portal.com / Admin@123\n";
        } else {
            // Update existing admin to ensure all fields are set
            $admin->update([
                'is_verified' => true,
                'is_approved' => true,
                'status' => 'approved',
                'admin_approved_at' => $admin->admin_approved_at ?? now(),
            ]);
            
            echo "✅ Admin user already exists and updated\n";
        }
    }
}
