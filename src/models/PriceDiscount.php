<?php

namespace Abs\PartPkg;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceDiscount extends Model
{
	use SoftDeletes;
    protected $table = "price_discounts";
}
