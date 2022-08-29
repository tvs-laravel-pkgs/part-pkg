<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerialNumberOptedColumnToPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            if (!Schema::hasColumn('parts', 'is_serial_no_opted')) {
                $table->tinyInteger('is_serial_no_opted')->after('tax_code_id')->nullable()->comment('1->Yes 0->No');

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
            if (Schema::hasColumn('parts', 'is_serial_no_opted')) {
                $table->dropColumn('is_serial_no_opted');
            }
        });
    }
}
