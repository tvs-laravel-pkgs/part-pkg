<?php

namespace Abs\PartPkg;

use Illuminate\Database\Eloquent\Model;

class PartVehicleDetail extends Model
{
    protected $table = 'part_vehicle_details';
	public $timestamps = false;

	public function vehicleModel() {
		return $this->belongsTo('App\VehicleModel', 'vehicle_model_id');
	}
}
