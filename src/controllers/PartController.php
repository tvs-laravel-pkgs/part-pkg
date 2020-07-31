<?php

namespace Abs\PartPkg;
use Abs\GigoPkg\TaxCode;
use App\Http\Controllers\Controller;
use App\Part;
use App\PartAlternate;
use App\PartUpsell;
use App\PartVehicleDetail;
use App\VehicleMake;
use App\VehicleModel;
use App\Uom;
use App\Config;
use App\Aggregate;
use App\SubAggregate;
use App\PartRack;
use Auth;
use Carbon\Carbon;
use DB;
use Entrust;
use Illuminate\Http\Request;
use Validator;
use Yajra\Datatables\Datatables;

class PartController extends Controller {

	public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}

	public function getPartList(Request $request) {
		$parts = Part::withTrashed()
			->select([
				'parts.id',
				'parts.name',
				'parts.code',
				'parts.rate',
				'uoms.code as uom',
				'tax_codes.code as tax_code',
				DB::raw('IF(parts.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->leftjoin('uoms', 'uoms.id', 'parts.uom_id')
			->leftjoin('tax_codes', 'tax_codes.id', 'parts.tax_code_id')
			->where('parts.company_id', Auth::user()->company_id)

			->where(function ($query) use ($request) {
				if (!empty($request->code)) {
					//dd('code');
					$query->where('parts.code', 'LIKE', '%' . $request->code . '%');
				}
			})
			->where(function ($query) use ($request) {
				if (!empty($request->name)) {
					//dd('name');
					$query->where('parts.name', 'LIKE', '%' . $request->name . '%');
				}
			})
			->where(function ($query) use ($request) {
				if (!empty($request->uom_filter_id)) {
					//dd('uom_id');
					$query->where('uoms.id', $request->uom_filter_id);
				}
			})
			->where(function ($query) use ($request) {
				if (!empty($request->tax_code_filter_id)) {
					//dd('tax_codes');
					$query->where('tax_codes.id', $request->tax_code_filter_id);
				}
			})

			->where(function ($query) use ($request) {
				if ($request->status == '1') {
					$query->whereNull('parts.deleted_at');
				} else if ($request->status == '0') {
					$query->whereNotNull('parts.deleted_at');
				}
			})
		;

		return Datatables::of($parts)
		// ->rawColumns(['name', 'action','status'])
			->addColumn('status', function ($part) {
				$status = $part->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $part->status;
			})
			->addColumn('action', function ($part) {
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$output = '';
				if (Entrust::can('edit-part')) {
					$output .= '<a href="#!/part-pkg/part/edit/' . $part->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';
				}
				if (Entrust::can('delete-part')) {
					$output .= '<a href="javascript:;" data-toggle="modal" data-target="#part-delete-modal" onclick="angular.element(this).scope().deletePart(' . $part->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';
				}
				return $output;
			})
			->make(true);
	}


	public function getPartFormData(Request $request) {
		$id = $request->id;
		//UPDATED BY KARTHICK T ON 15-07-2020
		$this->data['aggregate_list'] = collect(Aggregate::select('name', 'id')->get())->prepend(['id' => '', 'name' => 'Select Item Aggregate']);
		$this->data['sub_aggregate_list'] = [];
		$this->data['components_list'] = [];
		$this->data['vehicle_make_list'] = collect(VehicleMake::where('company_id', Auth::user()->company_id)->select('name', 'id')->groupBy('name')->get())->prepend(['id' => '', 'name' => 'Select Vehicle Make']);
		$this->data['year_list'] = collect(Config::where('config_type_id', 125)->select('name', 'id')->groupBy('name')->get())->prepend(['id' => '', 'name' => 'Select Vehicle Year']);
		$this->data['fuel_type_list'] = collect(Config::where('config_type_id', 126)->select('name', 'id')->groupBy('name')->get())->prepend(['id' => '', 'name' => 'Select Fuel Type']);
		$this->data['vehicle_type_list'] = collect(Config::where('config_type_id', 127)->select('name', 'id')->groupBy('name')->get())->prepend(['id' => '', 'name' => 'Select Vehicle Type']);

		$this->data['vehicle_model_list'] = new VehicleModel;
		
		if (!$id) {
			$part = new Part;
			$action = 'Add';
			$part->hsn_code = [];
			$part->vehicle_categories = [];
			$this->data['vehicle_mappings'] = new PartVehicleDetail();
			$this->data['alt_parts'] = [];
			$this->data['upsell_parts'] = [];
			$this->data['alt_parts_ids'] = [];
			$this->data['upsell_parts_ids'] = [];
			$this->data['part_attachments'] = [];
			//ADDED BY KARTHICK T ON 30-07-2020
			$this->data['rack_parts'] = [];
			//ADDED BY KARTHICK T ON 30-07-2020

		} else {
			$part = Part::select(
				'parts.*',
				'sub_aggregates.aggregate_id as aggregate_id',
				DB::raw('COALESCE(DATE_FORMAT(item_available_date,"%d-%m-%Y"), "--") as item_available_date')
			)->leftJoin('sub_aggregates','sub_aggregates.id','parts.sub_aggregate_id')
			->withTrashed()
			->find($id);
			$action = 'Edit';

			$part->hsn_code = TaxCode::where('id',$part->tax_code_id)
					->select('code', 'id')
					->first();
			$this->data['alt_parts'] = $alt_parts = Part::select(
				'parts.id',
				'parts.code',
				'parts.name',
				'parts.mrp',
				'parts.cost_price',
				'parts.list_price'
			)
				->leftjoin('part_alternate', 'parts.id', 'part_alternate.alternate_part_id')
				->where('part_id', $id)
				->get();
			$this->data['upsell_parts'] = $upsell_parts = Part::select(
				'parts.id',
				'parts.code',
				'parts.name',
				'parts.mrp',
				'parts.cost_price',
				'parts.list_price'
			)
				->leftjoin('part_upsell', 'parts.id', 'part_upsell.upsell_part_id')
				->where('part_id', $id)
				->get();

			$this->data['alt_parts_ids'] = PartAlternate::leftjoin('parts', 'parts.id', 'part_alternate.alternate_part_id')
				->where('part_id', $id)
				->pluck('parts.id');

			$this->data['upsell_parts_ids'] = PartUpsell::leftjoin('parts', 'parts.id', 'part_upsell.upsell_part_id')
				->where('part_id', $id)
				->pluck('parts.id');

			$vehicle_mappings = PartVehicleDetail::with(
				'vehicleModel',
				'vehicleModel.vehicleMake'
			)
				->where('part_id', $id)
				->get();

			$this->data['vehicle_mappings'] = $vehicle_mappings;

			//ADDED BY KARTHICK T ON 30-07-2020
			$this->data['rack_parts'] = PartRack::leftjoin('parts','parts.id','part_rack.part_id')
				->where('part_id',$id)
				->select('part_rack.*')
				->get();
			//ADDED BY KARTHICK T ON 30-07-2020

		}

		//UPDATED BY KARTHICK T ON 15-07-2020
		$this->data['success'] = true;
		$this->data['part'] = $part;
		$this->data['action'] = $action;
		$this->data['extras'] = [
			'uom_list' => Uom::getList(),
			'tax_code_list' => TaxCode::getList(),
		];
		return response()->json($this->data);
	}

	public function getPartFilterData() {
		$this->data['extras'] = [
			'uom_list' => Uom::getList(),
			'tax_code_list' => TaxCode::getList(),
			'status' => [
				['id' => '', 'name' => 'Select Status'],
				['id' => '1', 'name' => 'Active'],
				['id' => '0', 'name' => 'Inactive'],
			],
		];
		return response()->json($this->data);
	}

	public function savePart(Request $request) {
		// dd($request->all());
		try {
			$error_messages = [
				'code.required' => 'Code is Required',
				'code.unique' => 'Code is already taken',
				'code.min' => 'Code is Minimum 3 Charachers',
				'code.max' => 'Code is Maximum 32 Charachers',
				'name.unique' => 'Name is already taken',
				'name.min' => 'Name is Minimum 3 Charachers',
				'name.max' => 'Name is Maximum 191 Charachers',
				'display_order.unique' => 'Display Order is already taken',
			];
			$validator = Validator::make($request->all(), [
				'code' => [
					'required:true',
					'min:3',
					'max:32',
					'unique:parts,code,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'name' => [
					'min:3',
					'max:191',
					'nullable',
					'unique:parts,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'tax_code_id' => [
					'nullable',
					'exists:tax_codes,id',
				],
				'uom_id' => [
					'nullable',
					'exists:uoms,id',
				],
				'display_order' => [
					'unique:parts,display_order,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$part = new Part;
				$part->company_id = Auth::user()->company_id;
				$part->created_by_id = Auth::user()->id;
			} else {
				$part = Part::withTrashed()->find($request->id);
				$part->updated_by_id = Auth::user()->id;

				//If Rate differed previous rate it will change all service type parts amount
				if ($request->rate != $part->rate) {
					$this->changeServiceRate($part->id, $request->rate);
				}
			}
			$part->code = $request->code;
			$part->name = $request->name;
			$part->rate = $request->rate;
			//UPDATED BY KARTHICK T ON 30-07-2020
			$part->sub_aggregate_id = $request->sub_aggregate_id;
			$part->variant = $request->variant;
			$part->brand = $request->brand;
			$part->component = $request->component;
			//UPDATED BY KARTHICK T ON 30-07-2020
			$part->min_sale_order_qty = $request->min_sale_order_qty;
			$part->max_sale_order_qty = $request->max_sale_order_qty;
			$part->uom_id = $request->uom_id;
			$part->tax_code_id = $request->tax_code_id;
			$part->pack_size = $request->pack_size;
			$part->height = $request->height;
			$part->width = $request->width;
			$part->weight = $request->weight;
			$part->item_available_date = date('Y-m-d',strtotime($request->item_available_date));
			$part->item_name_in_local_lang = $request->item_name_in_local_lang;
			$part->product_video_link = $request->product_video_link;
			$part->mrp = $request->mrp;
			$part->list_price = $request->list_price;
			$part->cost_price = $request->cost_price;
			$part->display_order = $request->display_order;
			if ($request->status == 'Inactive') {
				$part->deleted_at = Carbon::now();
				$part->deleted_by_id = Auth::user()->id;
			} else {
				$part->deleted_at = NULL;
				$part->deleted_by_id = NULL;
			}

			$part->save();

			//Vehicle Part Store
			if(isset($request->vehicle_make_id) && count($request->vehicle_make_id) > 0){
				$delete_vehicle_details = PartVehicleDetail::where('part_id', $part->id)
					->forceDelete();
				foreach ($request->vehicle_make_id as $key => $make_id) {
					$alternate_part = PartVehicleDetail::updateOrInsert([
						'part_id' => $part->id, 
						'vehicle_make_id' => $make_id,
						'vehicle_model_id' => $request->vehicle_model_id[$key],
						'vehicle_year_id' => $request->years[$key],
						'fuel_type_id' => $request->fuel_type[$key],
						'vehicle_type_id' => $request->vehicle_type[$key]
					]);
				}
			}

			$alt_parts = [];
			if(isset($request->alternate_part_ids) && $request->alternate_part_ids != null){
				$alt_parts = explode(",", $request->alternate_part_ids);
			}
			//Alternate Part Store
			if(count($alt_parts) > 0){
				$delete_alt_part_details = PartAlternate::where('part_id', $part->id)
					->forceDelete();
				foreach ($alt_parts as $key => $alternate_part_id) {
					$alternate_part = PartAlternate::updateOrInsert([
						'part_id' => $part->id, 
						'alternate_part_id' => $alternate_part_id
					]);
				}
			}

			$upsell_parts = [];
			if(isset($request->upsell_part_ids) && $request->upsell_part_ids != null){
				$upsell_parts = explode(",", $request->upsell_part_ids);
			}
			//Upsell Part Store
			if(count($upsell_parts) > 0){
				$delete_upsell_part_details = PartUpsell::where('part_id', $part->id)
					->forceDelete();
				foreach ($upsell_parts as $key => $upsell_part_id) {
					$alternate_part = PartUpsell::updateOrInsert([
						'part_id' => $part->id, 
						'upsell_part_id' => $upsell_part_id
					]);
				}
			}

			//ADDED BY KARTHICK T ON 30-07-2020
			//Part Rack Store
			if(isset($request->rack_ids) && count($request->rack_ids) > 0){
				foreach ($request->rack_ids as $key => $rack_id) {
					$part_rack = PartRack::where('id',$rack_id)->first();
					if(!$part_rack){
						$part_rack = new PartRack();
					}
					$part_rack->part_id = $part->id;
					$part_rack->name = $request->rack_name[$key];
					$part_rack->quantity = $request->rack_qty[$key];
					$part_rack->save();
				}
			}
			//ADDED BY KARTHICK T ON 30-07-2020

			DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Part Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Part Updated Successfully',
				]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'error' => $e->getMessage(),
			]);
		}
	}

	public function changeServiceRate($part_id, $rate) {

		$service_types = DB::table('part_service_type')->where('part_service_type.part_id', $part_id)->select('quantity', 'amount', 'service_type_id')->get();

		foreach ($service_types as $key => $value) {
			$qty = $value->quantity;
			$amount = $qty * $rate;
			DB::table('part_service_type')->where('part_id', $part_id)->where('service_type_id', $value->service_type_id)->update(['amount' => $amount]);
		}

	}
	public function deletePart(Request $request) {
		DB::beginTransaction();
		// dd($request->id);
		try {
			$part = Part::withTrashed()->where('id', $request->id)->forceDelete();
			if ($part) {
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Part Deleted Successfully']);
			}
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}

	public function getParts(Request $request) {
		$parts = Part::withTrashed()
			->with([
				'parts',
				'parts.user',
			])
			->select([
				'parts.id',
				'parts.name',
				'parts.code',
				DB::raw('IF(parts.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('parts.company_id', Auth::user()->company_id)
			->get();

		return response()->json([
			'success' => true,
			'parts' => $parts,
		]);
	}

	//CREATED BY KARTHICK T ON 15-07-2020
	public function getItemSubAggregateByAggregate(Request $request) {
		if (!empty($request->part_category_id)) {
			$part_sub_categories_list = collect(
				SubAggregate::where('aggregate_id', $request->part_category_id)
					->select('name', 'id')
					->get()
				)
				->prepend(['id' => '', 'name' => 'Select Sub Aggregate']);
		} else {
			$part_sub_categories_list = [];
		}
		return response()->json(['part_sub_categories_list' => $part_sub_categories_list]);
	}
	public function getHsnCode(Request $request) {
		if (!empty($request->key)) {
			$tax_code_list = collect(
				TaxCode::where('code','like','%'. $request->key . '%')
					->where('type_id', 1020)	// HSN CODE
					->select('code', 'id')
					->get()
				);
		} else {
			$tax_code_list = [];
		}
		return response()->json(['tax_code_list' => $tax_code_list]);
	}
	public function getVehicleModelByMake(Request $request){
		if (!empty($request->vehicle_make_id)) {
			$vehicle_model_list = collect(
				VehicleModel::select('model_name as name', 'id')
					->where('vehicle_make_id', $request->vehicle_make_id)
					->groupBy('model_name')
					->get()
				)->prepend(['id' => '', 'name' => 'Select Vehicle Model']);
		} else {
			$vehicle_model_list = [];
		}
		return response()->json(['vehicle_model_list' => $vehicle_model_list]);
	}
	public function getNewPartDetail(Request $request){
		// dd($request->all());
		$ids = [];
		if(!empty($request->part_ids)){
			$ids = array_map('intval', explode(",", $request->part_ids));
		}
		if($request->id){
			array_push($ids, (int) $request->id);
		}
		if (!empty($request->key)) {
			$search_results = Part::where(function ($query) use ($request) {
					$query->where('code','like','%'. $request->key . '%')
						->orWhere('name', 'like', '%' . $request->key . '%');
				})
				->whereNotIn('id',$ids)
				->select(
					'id', 
					'code',
					'name'
				)
				->limit(10)
				->get();
		} else {
			$search_results = [];
		}
		return response()->json(['new_parts_list' => $search_results]);
	}
	public function addNewParts(Request $request){
		$this->data['new_parts'] = Part::select(
				'id', 
				'code',
				'name',
				'mrp',
				'cost_price',
				'list_price'
			)
			->where('id', $request->add_part_id)
			->first();

		return response()->json($this->data);
	}
	//CREATED BY KARTHICK T ON 15-07-2020
	//CREATED BY KARTHICK T ON 30-07-2020
	public function deletePartRack(Request $request){
		// dd($request->all());
		if($request->rack_id){
			$delete_rack = PartRack::where('id',$request->rack_id)->forceDelete();
		}
	}
	//CREATED BY KARTHICK T ON 30-07-2020
}