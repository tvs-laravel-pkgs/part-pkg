<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartRacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_rack', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('part_id');
            $table->string('name');
            $table->unsignedinteger('quantity');

            $table->foreign('part_id')->references('id')->on('parts')->onDelete('CASCADE')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_rack');
    }
}
