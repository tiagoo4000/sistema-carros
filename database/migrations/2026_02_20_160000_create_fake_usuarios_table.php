<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fake_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('apelido')->unique();
            $table->string('estado', 2)->nullable();
            $table->string('tag')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fake_usuarios');
    }
};

