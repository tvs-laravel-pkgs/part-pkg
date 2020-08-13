<?php

namespace Abs\PartPkg;

use Illuminate\Database\Eloquent\Model;

class SubAggregate extends Model
{
    protected $table = "sub_aggregates";
    protected $fillable = ['code'];
}
