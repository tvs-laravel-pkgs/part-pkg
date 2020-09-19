<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubAggregatesChangeUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('sub_aggregates', function (Blueprint $table) {
                $table->dropUnique('sub_aggregates_code_unique');
                $table->unique(["aggregate_id", "code"]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('sub_aggregates', function (Blueprint $table) {
			$table->dropUnique('sub_aggregates_aggregate_id_code_unique');
			$table->unique(["code"]);
		});
    }
}
