<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nomor_urut')->unique();
            $table->string('nama_ketua');
            $table->string('nama_wakil')->nullable();
            $table->string('visi', 500)->nullable();
            $table->text('misi')->nullable();
            $table->string('foto_ketua')->nullable();
            $table->string('foto_wakil')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
