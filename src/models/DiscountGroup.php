<?php

namespace Abs\PartPkg;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountGroup extends Model
{
	use SoftDeletes;
    protected $table = "discount_groups";
    protected $fillable = [
    	"name", 
    	"type_id", 
    	"company_id"
    ];
}
