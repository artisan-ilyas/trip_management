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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // required
            $table->string('legal_name')->nullable(); // optional
            $table->string('slug')->unique(); // unique
            $table->string('currency', 10); // e.g., USD, IDR
            $table->string('timezone', 100); // e.g., Asia/Makassar
            $table->string('billing_email'); // billing email
            $table->text('address')->nullable(); // optional
            $table->string('vat_tax_id')->nullable(); // optional

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
