<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UserSeeder::class);

        $this->call(MediaSeeder::class);
    
        $this->call(TwoFactorAuthEmailSeeder::class);
    
        $this->call(PasswordResetSeeder::class);
    
        $this->call(CategorySeeder::class);
    }
}