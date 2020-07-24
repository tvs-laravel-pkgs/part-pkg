<?php

namespace Abs\PartPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\BaseModel;
use App\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class Part extends BaseModel {
	use SeederTrait;
	use SoftDeletes;
	protected $table = 'parts';
	public $timestamps = true;
	protected $fillable =
		["id", "company_id", "code", "name", "uom_id", "rate", "tax_code_id"]
	;

	public static function relationships($action = 'index') {
		if ($action == 'index') {
			$relationships = [
				'uom',
				'taxCode',
				'repair_order_parts',
			];

		} else if ($action == 'read') {
			$relationships = [
				'uom',
				'taxCode',
				'taxCode.taxes',
				'repair_order_parts',
			];
		} else if ($action == 'options') {
			$relationships = [
				'uom',
				'taxCode',
				'repair_order_parts',
			];
		}

		return $relationships;
	}

	protected static $excelColumnRules = [
		'Code' => [
			'table_column_name' => 'code',
			'rules' => [
				'required' => [
				],
			],
		],
		'Name' => [
			'table_column_name' => 'name',
			'rules' => [
				'required' => [
				],
			],
		],
		'UOM Code' => [
			'table_column_name' => 'uom_id',
			'rules' => [
				'nullable' => [
				],
				'fk' => [
					'class' => 'App\Uom',
					'foreign_table_column' => 'code',
					'check_with_company' => true,
				],
			],
		],
		'Rate' => [
			'table_column_name' => 'rate',
			'rules' => [
				'required' => [
				],
				'unsigned_decimal' => [
					'size' => '12,2',
				],
			],
		],
		'MRP' => [
			'table_column_name' => 'mrp',
			'rules' => [
				'required' => [
				],
				'unsigned_decimal' => [
					'size' => '12,2',
				],
			],
		],
		'Tax Code' => [
			'table_column_name' => 'tax_code_id',
			'rules' => [
				'nullable' => [
				],
				'fk' => [
					'class' => 'App\TaxCode',
					'foreign_table_column' => 'code',
					'check_with_company' => true,
				],
			],
		],
	];

	// Query Scopes --------------------------------------------------------------

	public function scopeFilterSearch($query, $term) {
		if (strlen($term)) {
			$query->where(function ($query) use ($term) {
				$query->orWhere('code', 'LIKE', '%' . $term . '%');
				$query->orWhere('name', 'LIKE', '%' . $term . '%');
			});
		}
	}

	// Getter & Setters --------------------------------------------------------------

	public function getDateOfJoinAttribute($value) {
		return empty($value) ? '' : date('d-m-Y', strtotime($value));
	}

	public function setDateOfJoinAttribute($date) {
		return $this->attributes['date_of_join'] = empty($date) ? NULL : date('Y-m-d', strtotime($date));
	}

	// Relations --------------------------------------------------------------

	public function uom() {
		return $this->belongsTo('App\Uom', 'uom_id');
	}
	public function taxCode() {
		return $this->belongsTo('Abs\TaxPkg\TaxCode', 'tax_code_id');
	}
	public function repair_order() {
		return $this->belongsToMany('App\RepairOrder');
	}

	public function repair_order_parts() {
		return $this->belongsToMany('App\RepairOrder', 'repair_order_part', 'part_id', 'repair_order_id');
	}

	// Static operations --------------------------------------------------------------

	public static function saveFromObject($record_data) {
		$record = [
			'Company Code' => $record_data->company_code,
			'Code' => $record_data->code,
			'Name' => $record_data->name,
			'UOM Code' => $record_data->uom_code,
			'Rate' => $record_data->rate,
			'MRP' => $record_data->mrp,
			'Tax Code' => $record_data->tax_code,
		];
		return static::saveFromExcelArray($record);
	}

	public static function saveFromExcelArray($record_data) {
		$errors = [];
		$company = Company::where('code', $record_data['Company Code'])->first();
		if (!$company) {
			return [
				'success' => false,
				'errors' => ['Invalid Company : ' . $record_data['Company Code']],
			];
		}

		if (!isset($record_data['created_by_id'])) {
			$admin = $company->admin();

			if (!$admin) {
				return [
					'success' => false,
					'errors' => ['Default Admin user not found'],
				];
			}
			$created_by_id = $admin->id;
		} else {
			$created_by_id = $record_data['created_by_id'];
		}

		if (empty($record_data['Rate'])) {
			$errors[] = 'Rate is empty';
		} else {
			$rate = $record_data['Rate'];
		}

		if (empty($record_data['MRP'])) {
			$errors[] = 'MRP is empty';
		} else {
			$mrp = $record_data['MRP'];
		}

		if (count($errors) > 0) {
			return [
				'success' => false,
				'errors' => $errors,
			];
		}

		$record = Self::firstOrNew([
			'company_id' => $company->id,
			'code' => $record_data['Code'],
		]);

		$result = Self::validateAndFillExcelColumns($record_data, Static::$excelColumnRules, $record);
		if (!$result['success']) {
			return $result;
		}
		$record->mrp = $mrp;
		$record->rate = $rate;
		$record->created_by_id = $created_by_id;
		$record->save();
		return [
			'success' => true,
		];
	}

	public static function getList($params = [], $add_default = true, $default_text = 'Select Part') {
		$list = Collect(Self::select([
			'id',
			'name',
		])
				->orderBy('name')
				->get());
		if ($add_default) {
			$list->prepend(['id' => '', 'name' => $default_text]);
		}
		return $list;
	}

	public static function searchPart($r) {
		$key = $r->key;
		$list = self::select(
			'id',
			'code',
			'name'
		)
			->where(function ($q) use ($key) {
				$q->where('name', 'like', $key . '%')
					->orwhere('code', 'like', $key . '%')
				;
			})
			->get();
		return response()->json($list);
	}

}
