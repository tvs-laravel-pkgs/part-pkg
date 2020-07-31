<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVariantBrandComponentInPart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign('parts_category_id_foreign');
            $table->dropForeign('parts_sub_category_id_foreign');
            $table->dropColumn('category_id');
            $table->dropColumn('sub_category_id');

            $table->unsignedinteger('sub_aggregate_id')->nullable()->comment('Used in vims parts request')->after('tax_code_id');
            $table->string('variant')->nullable()->comment('Used in vims parts request')->after('sub_aggregate_id');
            $table->string('brand')->nullable()->comment('Used in vims parts request')->after('variant');
            $table->string('component')->nullable()->comment('Used in vims parts request')->after('brand');

            $table->foreign("sub_aggregate_id")->references("id")->on("sub_aggregates")->onDelete("CASCADE")->onUpdate("CASCADE");
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
            $table->dropForeign('parts_sub_aggregate_id_foreign');
            $table->dropColumn('sub_aggregate_id');
            $table->dropColumn('variant');
            $table->dropColumn('brand');
            $table->dropColumn('component');

            $table->unsignedinteger('category_id')->nullable()->comment('Used in vims parts request')->after('tax_code_id');
            $table->unsignedinteger('sub_category_id')->nullable()->comment('Used in vims parts request')->after('category_id');
            $table->foreign("category_id")->references("id")->on("service_item_categories")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("sub_category_id")->references("id")->on("service_item_sub_categories")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
    }
}
