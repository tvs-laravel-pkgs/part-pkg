<?php

namespace Abs\PartPkg;

use Illuminate\Database\Eloquent\Model;

class PartRack extends Model
{
    protected $table = 'part_rack';
	public $timestamps = false;
	protected $fillable = ['part_id','part_rack_id'];
}
