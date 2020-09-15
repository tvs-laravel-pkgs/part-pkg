<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('price_discounts')) {
            Schema::create('price_discounts', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('company_id');
                $table->unsignedInteger('region_id');
                $table->unsignedInteger('discount_group_id');
                $table->unsignedDecimal('purchase_discount', 8, 2)->nullable();
                $table->unsignedDecimal('approved_discount', 8, 2)->nullable();
                $table->unsignedDecimal('customer_discount', 8, 2)->nullable();

                $table->unsignedInteger('created_by_id');
                $table->unsignedInteger('updated_by_id')->nullable();
                $table->unsignedInteger('deleted_by_id')->nullable();
                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();

                $table->foreign("company_id")->references("id")->on("companies")->onDelete("CASCADE")->onUpdate("CASCADE");
                $table->foreign("region_id")->references("id")->on("regions")->onDelete("CASCADE")->onUpdate("CASCADE");
                $table->foreign("discount_group_id")->references("id")->on("discount_groups")->onDelete("CASCADE")->onUpdate("CASCADE");
                $table->foreign('created_by_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
                $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');
                $table->foreign('deleted_by_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('cascade');

                $table->unique(["company_id", "region_id", "discount_group_id"]);
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
        Schema::dropIfExists('price_discounts');
    }
}
