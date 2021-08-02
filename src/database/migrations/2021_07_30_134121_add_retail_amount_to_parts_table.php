<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRetailAmountToPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            if (!Schema::hasColumn('parts','retail_price')) {
                $table->unsignedDecimal('mrp', 12, 2)->nullable()->comment('Regular Price Used in vims parts request')->change();
                $table->unsignedDecimal('retail_price', 12, 2)->after('mrp')->nullable()->comment('Retail Price Used in vims parts request');
            }
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
            if (Schema::hasColumn('parts','retail_price')) {
                $table->dropColumn('retail_price');
            }
        });
    }
}
