<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteAtForAggregateSubAggregateRack extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aggregates', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });
        Schema::table('sub_aggregates', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });
        Schema::table('racks', function (Blueprint $table) {
            $table->unsignedinteger('deleted_by')->after('updated_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aggregates', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('sub_aggregates', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('racks', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropForeign('racks_deleted_by_foreign');
            $table->dropColumn('deleted_by');
        });
    }
}
