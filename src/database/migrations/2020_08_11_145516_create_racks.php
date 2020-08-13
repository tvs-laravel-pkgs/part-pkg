<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('racks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('company_id');
            $table->unsignedinteger('outlet_id');
            $table->unsignedinteger('type_id');
            $table->string('name');
            $table->unsignedinteger('created_by');
            $table->unsignedinteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign("company_id")->references("id")->on("companies")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("outlet_id")->references("id")->on("outlets")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("type_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("updated_by")->references("id")->on("users")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('racks');
    }
}
