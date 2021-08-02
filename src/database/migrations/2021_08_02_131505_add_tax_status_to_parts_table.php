<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxStatusToPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            if (!Schema::hasColumn('parts','tax_status')) {
                $table->tinyInteger('tax_status')->default(1)->after('uom_id')->comment('0 - No, 1 - Yes');
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
            if (!Schema::hasColumn('parts','tax_status')) {
                $table->dropColumn('tax_status');
            }
        });
    }
}
