<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrandVariantComponentInParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('variant');
            $table->dropColumn('brand');
            $table->dropColumn('component');

            $table->unsignedinteger('variant_id')->nullable()->comment('Used in vims parts request')->after('sub_aggregate_id');
            $table->unsignedinteger('brand_id')->nullable()->comment('Used in vims parts request')->after('variant_id');
            $table->unsignedinteger('component_id')->nullable()->comment('Used in vims parts request')->after('brand_id');

            $table->foreign("variant_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("brand_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("component_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
        Schema::table('part_rack', function (Blueprint $table) {
            $table->dropColumn('name');

            $table->unsignedinteger('part_rack_id')->nullable()->after('part_id');
            $table->foreign("part_rack_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
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
            $table->dropForeign('parts_variant_id_foreign');
            $table->dropForeign('parts_brand_id_foreign');
            $table->dropForeign('parts_component_id_foreign');
            $table->dropColumn('variant_id');
            $table->dropColumn('brand_id');
            $table->dropColumn('component_id');

            $table->unsignedinteger('category_id')->nullable()->comment('Used in vims parts request')->after('tax_code_id');
            $table->unsignedinteger('sub_category_id')->nullable()->comment('Used in vims parts request')->after('category_id');
            $table->foreign("category_id")->references("id")->on("service_item_categories")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->string('variant')->nullable()->comment('Used in vims parts request')->after('sub_aggregate_id');
            $table->string('brand')->nullable()->comment('Used in vims parts request')->after('variant');
            $table->string('component')->nullable()->comment('Used in vims parts request')->after('brand');
        });
        Schema::table('part_rack', function (Blueprint $table) {
            $table->dropForeign('part_rack_part_rack_id_foreign');
            $table->dropColumn('part_rack_id');

            $table->string('name')->nullable()->after('part_id');
        });
    }
}
