<?php 

use Illuminate\Database\Seeder;
use App\Models;

class PasswordResetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\PasswordReset::class)->create([]);

    }
}
