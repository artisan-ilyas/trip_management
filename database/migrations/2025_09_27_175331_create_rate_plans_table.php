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
    Schema::create('rate_plans', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('company_id')->nullable();
        $table->unsignedBigInteger('boat_id')->nullable();
        $table->string('name');
        $table->string('currency', 3)->default('USD');
        $table->enum('base_price_type', ['per_room','charter'])->default('per_room');
        $table->boolean('tax_included')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_plans');
    }
};
