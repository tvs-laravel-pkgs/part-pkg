<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeyMappingForPartRack extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('part_rack', function (Blueprint $table) {
            $table->dropForeign('part_rack_part_rack_id_foreign');
            $table->dropColumn('part_rack_id');
        });
        Schema::table('part_rack', function (Blueprint $table) {
            $table->unsignedinteger('part_rack_id')->nullable()->after('part_id');
            $table->foreign("part_rack_id")->references("id")->on("racks")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('part_rack', function (Blueprint $table) {
            $table->dropForeign('part_rack_part_rack_id_foreign');
            $table->dropColumn('part_rack_id');
        });
        Schema::table('part_rack', function (Blueprint $table) {
            $table->unsignedinteger('part_rack_id')->nullable()->after('part_id');
            $table->foreign("part_rack_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
        });
    }
}
