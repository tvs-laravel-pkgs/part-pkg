<?php

namespace Abs\PartPkg;

use Illuminate\Database\Eloquent\Model;

class DiscountGroup extends Model
{
    protected $table = "discount_groups";
    protected $fillable = [
    	"name", 
    	"type_id", 
    	"company_id"
    ];
}
