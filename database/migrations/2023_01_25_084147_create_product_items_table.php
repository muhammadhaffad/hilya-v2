<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->string('gender');
            $table->string('age');
            $table->string('size');
            $table->string('color');
            $table->integer('price');
            $table->integer('weight')->default(100);
            $table->integer('discount')->default(0);
            $table->integer('stock');
            $table->string('note_bene')->nullable();
            $table->boolean('is_bundle')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_items');
    }
};
