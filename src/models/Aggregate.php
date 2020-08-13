<?php

namespace Abs\PartPkg;

use Illuminate\Database\Eloquent\Model;

class Aggregate extends Model
{
    protected $table = "aggregates";
    protected $fillable = ['code'];
}
