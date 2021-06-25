<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryColumnToPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('parts', 'category_id')) {
            Schema::table('parts', function (Blueprint $table) {
                //
                $table->unsignedInteger('category_id')->after('part_type_id')->comment('Used in vims parts request')->nullable();
                $table->unsignedTinyInteger('tcs_status')->default(0)->after('mitr_item_flag');
                $table->unsignedDecimal('tcs_amount',12,2)->after('tcs_status')->nullable();

                $table->foreign("category_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('parts', 'category_id')) {
            Schema::table('parts', function (Blueprint $table) {
                //
                $table->dropForeign('parts_category_id_foreign');
                $table->dropColumn('category_id');
                $table->dropColumn('tcs_status');
                $table->dropColumn('tcs_amount');
            });
        }
    }
}
