<?php

namespace Abs\PartPkg;

use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    protected $table = 'racks';
    public $fillable = ['name','type_id'];
}
