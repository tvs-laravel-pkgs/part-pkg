<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartRolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_rols', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('part_id');
            $table->unsignedInteger('outlet_id');
            $table->integer('sbu_id');
            $table->unsignedInteger('rol_qty');
            $table->timestamps();
            $table->foreign('part_id')->references('id')->on('parts')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('CASCADE')->onUpdate('cascade');
            $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('CASCADE')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_rols');
    }
}
