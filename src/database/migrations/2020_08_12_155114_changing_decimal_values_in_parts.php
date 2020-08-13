<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangingDecimalValuesInParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->unsignedDecimal('discount', 5, 2)->nullable()->comment('Used in vims parts request')->change();
            $table->unsignedDecimal('cdndv', 5, 2)->nullable()->comment('Used in vims parts request')->change();
            $table->unsignedDecimal('retail_discount', 5, 2)->nullable()->comment('Used in vims parts request')->change();
            $table->unsignedDecimal('gst', 5, 2)->nullable()->comment('Used in vims parts request')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->unsignedDecimal('discount', 2, 2)->nullable()->comment('Used in vims parts request')->change();
            $table->unsignedDecimal('cdndv', 2, 2)->nullable()->comment('Used in vims parts request')->change();
            $table->unsignedDecimal('retail_discount', 2, 2)->nullable()->comment('Used in vims parts request')->change();
            $table->unsignedDecimal('gst', 2, 2)->nullable()->comment('Used in vims parts request')->change();
        });
    }
}
