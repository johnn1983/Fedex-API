<?php 

use Illuminate\Database\Seeder;
use App\Models;

class TwoFactorAuthEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\TwoFactorAuthEmail::class)->create([]);

    }
}
