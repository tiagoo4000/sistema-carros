<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('usuarios')->where('email', 'like', '%@example.%')->delete();
    }

    public function down(): void
    {
        //
    }
};

