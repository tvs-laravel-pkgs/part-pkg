<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartsDetailsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_vehicle_details', function (Blueprint $table) {
            $table->unsignedinteger('part_id');
            $table->unsignedinteger('vehicle_make_id')->nullable();
            $table->unsignedinteger('vehicle_model_id')->nullable();
            $table->unsignedinteger('vehicle_year_id')->nullable();
            $table->unsignedinteger('fuel_type_id')->nullable();
            $table->unsignedinteger('vehicle_type_id')->nullable();


            $table->foreign("part_id")->references("id")->on("parts")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("vehicle_make_id")->references("id")->on("vehicle_makes")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("vehicle_model_id")->references("id")->on("models")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("vehicle_year_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("fuel_type_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("vehicle_type_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
        Schema::create('part_alternate', function (Blueprint $table) {
            $table->unsignedinteger('part_id');
            $table->unsignedinteger('alternate_part_id');

            $table->foreign("part_id")->references("id")->on("parts")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("alternate_part_id")->references("id")->on("parts")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
        Schema::create('part_upsell', function (Blueprint $table) {
            $table->unsignedinteger('part_id');
            $table->unsignedinteger('upsell_part_id');

            $table->foreign("part_id")->references("id")->on("parts")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("upsell_part_id")->references("id")->on("parts")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_vehicle_details');
        Schema::dropIfExists('part_alternate');
        Schema::dropIfExists('part_upsell');
    }
}
