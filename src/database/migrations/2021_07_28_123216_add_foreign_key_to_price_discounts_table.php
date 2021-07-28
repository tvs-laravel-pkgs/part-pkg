<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToPriceDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_discounts', function (Blueprint $table) {
            $table->foreign("company_id")->references("id")->on("companies")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("region_id")->references("id")->on("regions")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("discount_group_id")->references("id")->on("discount_groups")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_discounts', function (Blueprint $table) {
            //
            $table->dropForeign('price_discounts_company_id_foreign');
            $table->dropForeign('price_discounts_region_id_foreign');
            $table->dropForeign('price_discounts_discount_group_id_foreign');
        });
    }
}
