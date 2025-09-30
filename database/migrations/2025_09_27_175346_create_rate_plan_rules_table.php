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
    Schema::create('rate_plan_rules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rate_plan_id')->constrained('rate_plans')->cascadeOnDelete();
        $table->unsignedBigInteger('room_id')->nullable();
        $table->decimal('base_price', 12, 2);
        $table->decimal('extra_bed_price', 12, 2)->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_plan_rules');
    }
};
