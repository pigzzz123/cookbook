<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCookBookFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cook_book_foods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('book_id')->nullable()->comment('菜谱 id');
            $table->foreign('book_id')->references('id')->on('cook_books')->onDelete('cascade');
            $table->unsignedBigInteger('food_id')->nullable()->comment('食材 id');
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
            $table->string('number')->nullable()->comment('数量');
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
        Schema::dropIfExists('cook_book_foods');
    }
}
