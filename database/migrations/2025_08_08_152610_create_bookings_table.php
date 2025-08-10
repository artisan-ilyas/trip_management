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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->unsignedBigInteger('trip_id');

            // Booking details
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->date('booking_date');
            $table->integer('number_of_guests')->default(1);
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('payment_status')->default('pending'); // pending, paid, cancelled
            $table->string('booking_status')->default('confirmed'); // confirmed, cancelled, completed

            // Your existing fields
            $table->string('source');
            $table->text('notes')->nullable();
            $table->string('comments')->nullable();

            // Extra integration fields
            $table->string('guest_form_token')->nullable();
            $table->string('guest_form_url')->nullable();

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('trip_id')
                  ->references('id')
                  ->on('trips')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
