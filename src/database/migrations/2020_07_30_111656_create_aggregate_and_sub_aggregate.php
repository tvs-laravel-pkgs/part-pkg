<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAggregateAndSubAggregate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->unsignedinteger('created_by');
            $table->unsignedinteger('updated_by')->nullable();
            $table->unsignedinteger('deleted_by')->nullable();
            $table->timestamps();

            $table->unique('code');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
        });
        Schema::create('sub_aggregate', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('aggregate_id');
            $table->string('code');
            $table->string('name');
            $table->unsignedinteger('created_by');
            $table->unsignedinteger('updated_by')->nullable();
            $table->unsignedinteger('deleted_by')->nullable();
            $table->timestamps();

            $table->unique('code');
            $table->foreign('aggregate_id')->references('id')->on('aggregate')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aggregate');
        Schema::dropIfExists('sub_aggregate');
    }
}
