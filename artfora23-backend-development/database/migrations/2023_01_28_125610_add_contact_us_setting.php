<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $value = json_encode(['email' => '']);

        DB::insert("
          INSERT INTO settings (name, value, is_public)
          VALUES ('contact_us', '{$value}'::jsonb, false)
        ");
    }

    public function down()
    {
        //
    }
};
