<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPOColumnsToPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->unsignedinteger('category_id')->nullable()->comment('Used in vims parts request')->after('tax_code_id');
            $table->unsignedinteger('sub_category_id')->nullable()->comment('Used in vims parts request')->after('category_id');
            $table->unsignedDecimal('min_sale_order_qty', 8, 2)->nullable()->comment('Used in vims parts request')->after('sub_category_id');
            $table->unsignedDecimal('max_sale_order_qty', 8, 2)->nullable()->comment('Used in vims parts request')->after('min_sale_order_qty');
            $table->unsignedDecimal('pack_size', 8, 2)->nullable()->comment('Used in vims parts request')->after('max_sale_order_qty');
            $table->unsignedDecimal('height', 8, 2)->nullable()->comment('Used in vims parts request')->after('pack_size');
            $table->unsignedDecimal('width', 8, 2)->nullable()->comment('Used in vims parts request')->after('height');
            $table->unsignedDecimal('weight', 8, 2)->nullable()->comment('Used in vims parts request')->after('width');
            $table->date('item_available_date')->nullable()->comment('Used in vims parts request')->after('weight');
            $table->string('item_name_in_local_lang',191)->nullable()->comment('Used in vims parts request')->after('item_available_date');
            $table->string('product_video_link',191)->nullable()->comment('Used in vims parts request')->after('item_name_in_local_lang');
            
            $table->unsignedDecimal('list_price', 12, 2)->nullable()->comment('Used in vims parts request')->after('product_video_link');
            $table->unsignedDecimal('cost_price', 12, 2)->nullable()->comment('Used in vims parts request')->after('list_price');
            $table->unsignedDecimal('discount', 2, 2)->nullable()->comment('Used in vims parts request')->after('cost_price');
            $table->unsignedDecimal('cdndv', 2, 2)->nullable()->comment('Used in vims parts request')->after('discount');
            $table->unsignedDecimal('retail_discount', 2, 2)->nullable()->comment('Used in vims parts request')->after('cdndv');
            $table->unsignedDecimal('retail_ndv', 8, 2)->nullable()->comment('Used in vims parts request')->after('retail_discount');
            $table->unsignedDecimal('gst', 2, 2)->nullable()->comment('Used in vims parts request')->after('retail_ndv');
            $table->boolean('nls')->default(0)->comment('0-No,1-Yes')->after('gst');
            $table->unsignedinteger('max_vor_qty')->nullable()->comment('Used in vims parts request')->after('nls');
            $table->boolean('aor')->default(0)->comment('0-No,1-Yes')->after('max_vor_qty');
            $table->unsignedinteger('moq')->nullable()->comment('Used in vims parts request')->after('aor');
            $table->unsignedinteger('mdq')->nullable()->comment('Used in vims parts request')->after('moq');
            $table->boolean('mitr_item_flag')->default(0)->comment('0-No,1-Yes')->after('mdq');
            $table->unsignedinteger('old_part_number')->nullable()->comment('Used in vims parts request')->after('mitr_item_flag');
            $table->unsignedinteger('display_order')->nullable()->comment('Used in vims parts request')->after('old_part_number');

            $table->unique('display_order');
            $table->foreign("category_id")->references("id")->on("service_item_categories")->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->foreign("sub_category_id")->references("id")->on("service_item_sub_categories")->onDelete("CASCADE")->onUpdate("CASCADE");
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
            $table->dropUnique('parts_display_order_unique');
            $table->dropForeign('parts_category_id_foreign');
            $table->dropForeign('parts_sub_category_id_foreign');

            $table->dropColumn('category_id');
            $table->dropColumn('sub_category_id');
            $table->dropColumn('min_sale_order_qty');
            $table->dropColumn('max_sale_order_qty');
            $table->dropColumn('pack_size');
            $table->dropColumn('height');
            $table->dropColumn('width');
            $table->dropColumn('weight');
            $table->dropColumn('item_available_date');
            $table->dropColumn('item_name_in_local_lang');
            $table->dropColumn('product_video_link');
            $table->dropColumn('list_price');
            $table->dropColumn('cost_price');
            $table->dropColumn('discount');
            $table->dropColumn('cdndv');
            $table->dropColumn('retail_discount');
            $table->dropColumn('retail_ndv');
            $table->dropColumn('gst');
            $table->dropColumn('nls');
            $table->dropColumn('max_vor_qty');
            $table->dropColumn('aor');
            $table->dropColumn('moq');
            $table->dropColumn('mdq');
            $table->dropColumn('mitr_item_flag');
            $table->dropColumn('old_part_number');
            $table->dropColumn('display_order');
        });
    }
}
