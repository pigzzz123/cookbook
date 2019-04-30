<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('名称');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('父类目 id');
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->boolean('is_directory')->comment('是否存在子类目');
            $table->unsignedInteger('level')->comment('当前类目层级');
            $table->string('path')->comment('当前类目所有父类目 id');
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
        Schema::dropIfExists('foods');
    }
}
