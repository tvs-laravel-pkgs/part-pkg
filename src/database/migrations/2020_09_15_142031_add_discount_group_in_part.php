<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscountGroupInPart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('discount_groups')) {
            Schema::create('discount_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('company_id');
                $table->unsignedInteger('type_id');
                $table->string('code');
                $table->string('name');

                $table->unsignedInteger('created_by_id');
                $table->unsignedInteger('updated_by_id')->nullable();
                $table->unsignedInteger('deleted_by_id')->nullable();
                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();

                $table->foreign("company_id")->references("id")->on("companies")->onDelete("CASCADE")->onUpdate("CASCADE");
                $table->foreign("type_id")->references("id")->on("configs")->onDelete("CASCADE")->onUpdate("CASCADE");
                $table->foreign('created_by_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
                $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
                $table->foreign('deleted_by_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');

                $table->unique(["type_id", "code"]);
            });
        }
        if (!Schema::hasColumn('parts', 'discount_group_id')){
            Schema::table('parts', function (Blueprint $table) {
                $table->unsignedInteger('discount_group_id')->nullable()->comment('Used in vims parts request')->after('description');

                $table->foreign('discount_group_id')->references('id')->on('discount_groups')->onDelete('CASCADE')->onUpdate('cascade');
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
        if (Schema::hasColumn('parts', 'discount_group_id')){
            Schema::table('parts', function (Blueprint $table) {
                $table->dropForeign('parts_discount_group_id_foreign');
                
                $table->dropColumn('discount_group_id');
            });
        }
        Schema::dropIfExists('discount_groups');
    }
}
