<?php

namespace Abs\PartPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartPricingDetail extends BaseModel
{
    use SeederTrait;
    use softDeletes;
    protected $table = 'part_pricing_details';
    public $timestamps = true;
}
