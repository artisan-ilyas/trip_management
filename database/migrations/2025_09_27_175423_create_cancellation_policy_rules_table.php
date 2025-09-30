<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cancellation_policy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cancellation_policy_id')->constrained('cancellation_policies')->cascadeOnDelete();
            $table->integer('days_from'); // inclusive
            $table->integer('days_to');   // inclusive
            $table->unsignedInteger('penalty_percent')->default(100);
            $table->boolean('refundable')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellation_policy_rules');
    }
};
