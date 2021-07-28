<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEffectiveFromToPriceDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('price_discounts','effective_from','effective_to')) {
                $table->date('effective_from')->after('customer_discount')->nullable();
                $table->date('effective_to')->after('effective_from')->nullable();
            }
            $table->dropForeign('price_discounts_region_id_foreign');
            $table->dropForeign('price_discounts_discount_group_id_foreign');
            $table->dropUnique('price_discounts_company_id_region_id_discount_group_id_unique');
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
            if (Schema::hasColumn('price_discounts','effective_from')) {
                $table->dropColumn('effective_from');
                $table->dropColumn('effective_to');
            }
            $table->foreign("region_id")->references("id")->on("regions")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("discount_group_id")->references("id")->on("discount_groups")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->unique(['company_id','region_id','discount_group_id']);

        });
    }
}
