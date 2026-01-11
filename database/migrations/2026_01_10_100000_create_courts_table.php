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
    Schema::create('courts', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->enum('floor_type', ['Karpet', 'Kayu']);
        $table->string('photo')->nullable();
        $table->integer('price_per_hour');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
