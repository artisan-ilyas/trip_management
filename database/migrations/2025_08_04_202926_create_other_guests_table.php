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
        Schema::create('other_guests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('guest_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('gender');
    $table->string('email');
    $table->string('password');
    $table->date('dob');
    $table->string('passport');
    $table->string('nationality');
    $table->string('cabin');
    $table->string('surfLevel');
    $table->text('boardDetails')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_guests');
    }
};
