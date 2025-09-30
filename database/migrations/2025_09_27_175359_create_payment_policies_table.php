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
    Schema::create('payment_policies', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('company_id')->nullable();
        $table->string('name');
        $table->unsignedInteger('dp_percent')->default(30);
        $table->unsignedInteger('balance_days_before_start')->default(7);
        $table->boolean('auto_cancel_if_dp_overdue')->default(false);
        $table->unsignedInteger('grace_days')->default(3);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_policies');
    }
};
