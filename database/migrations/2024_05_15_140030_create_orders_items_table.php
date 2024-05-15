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
        Schema::create('orderItems', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("item_id");
            $table->foreign('item_id')->references('id')->on('dishes');

            $table->integer("quantity");
            $table->string("options");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderItems');
    }
};
