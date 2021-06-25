<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_item_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('from_category_id');
            $table->unsignedInteger('to_category_id');
            $table->text('remarks')->nullable();
            $table->unsignedInteger('created_by_id');
            $table->unsignedInteger('updated_by_id')->nullable();
            $table->unsignedInteger('deleted_by_id')->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('parts')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('from_category_id')->references('id')->on('configs')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('to_category_id')->references('id')->on('configs')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('deleted_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_item_categories');
    }
}
