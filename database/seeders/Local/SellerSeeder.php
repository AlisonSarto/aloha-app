<?php

namespace Database\Seeders\Local;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name'     => 'Alison',
            'email'    => 'alison@email.com',
            'password' => Hash::make('123'),
        ]);

        $user->assignRole('seller');

        Seller::create([
            'user_id'               => $user->id,
            'phone'                 => '(00) 00000-0000',
            'commission_new_client' => 5.00,
            'commission_recurring'  => 2.50,
        ]);
    }
}
