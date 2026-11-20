<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultNonStudentUsersSeeder extends Seeder
{
    /** Create the demonstration accounts needed to access the enrollment app. */
    public function run(): void
    {
        foreach ([
            ['role' => 'admin', 'username' => 'admin', 'first_name' => 'Enrollment', 'last_name' => 'Administrator'],
            ['role' => 'guidance counselor', 'username' => 'guidance', 'first_name' => 'Guidance', 'last_name' => 'Counselor'],
            ['role' => 'principal', 'username' => 'principal', 'first_name' => 'School', 'last_name' => 'Principal'],
            ['role' => 'teacher', 'username' => 'teacher', 'first_name' => 'Sample', 'last_name' => 'Teacher'],
        ] as $account) {
            $roleId = Role::query()->where('role_name', $account['role'])->value('id');

            Staff::query()->updateOrCreate(
                ['username' => $account['username']],
                [
                    'role_id' => $roleId,
                    'first_name' => $account['first_name'],
                    'last_name' => $account['last_name'],
                    'email' => $account['username'].'@example.com',
                    'password' => Hash::make('password'),
                    // Demo accounts should open their dashboard immediately.
                    'change_password' => false,
                    'password_changed_at' => now(),
                    'status' => 'active',
                ],
            );
        }
    }
}
