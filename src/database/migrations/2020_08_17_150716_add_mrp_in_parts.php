<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMrpInParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('parts', 'mrp')){
            Schema::table('parts', function (Blueprint $table) {
                $table->unsignedDecimal('mrp', 12, 2)->nullable()->comment('Used in vims parts request')->after('tax_code_id');
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
        if (Schema::hasColumn('parts', 'mrp')){
            Schema::table('parts', function (Blueprint $table) {
                $table->dropColumn('mrp');
            });
        }
    }
}
