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
       Schema::create('guests', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('gender');
    $table->string('email');
    $table->date('dob');
    $table->string('passport');
    $table->string('nationality');
    $table->string('cabin');
    $table->string('surfLevel');
    $table->text('boardDetails')->nullable();

    // Travel Details
    $table->date('arrivalFlightDate');
    $table->string('arrivalFlightNumber');
    $table->string('arrivalAirport');
    $table->time('arrivalTime');
    $table->text('hotelPickup')->nullable();
    $table->date('departureFlightDate');
    $table->string('departureFlightNumber');
    $table->string('departureAirport');
    $table->time('departureTime');

    // Preferences & Health
    $table->text('medicalDietary')->nullable();
    $table->text('specialRequests')->nullable();
    $table->string('insuranceName')->nullable();
    $table->string('policyNumber')->nullable();
    $table->string('emergencyName')->nullable();
    $table->string('emergencyRelation')->nullable();
    $table->string('emergencyPhone')->nullable();
    $table->string('guestWhatsapp')->nullable();
    $table->string('guestEmail')->nullable();

    // Files
    $table->string('image_path')->nullable();
    $table->string('pdf_path')->nullable();
    $table->string('video_path')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
