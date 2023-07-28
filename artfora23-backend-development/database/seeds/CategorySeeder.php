<?php 

use Illuminate\Database\Seeder;
use App\Models;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Category::class)->create([]);

    }
}
