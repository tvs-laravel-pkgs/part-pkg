<?php

namespace Abs\PartPkg;

use Abs\PartPkg\Rack;
use App\Config;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Validator;
use Yajra\Datatables\Datatables;

class RackController extends Controller
{
	public function getRackFilterDetails(){
		$this->data['outlet_list'] = collect(
			Outlet::select(
				'code as name', 'id'
			)->where('company_id', Auth::user()->company_id)
			->get()
		)->prepend(['id' => '', 'name' => 'Select Outlet']);
		return response()->json($this->data);
	}
    public function getRackList(Request $request){
    	// dd($request->all());
    	$rack_list = Rack::select(
				'racks.id',
				'racks.name',
				'configs.name as type',
				DB::raw('IF(racks.deleted_at IS NULL, "Active","Inactive") as status')
			)
    		->leftJoin('configs','configs.id','racks.type_id')
			->where(function ($query) use ($request) {
				if (!empty($request->outlet)) {
					$query->where('racks.outlet_id', '=', $request->outlet);
				}
			})
			->where(function ($query) use ($request) {
				if (!empty($request->date_range)) {
					$dates = explode(' - ', $request->date_range);
					$converted_start_date = date_create_from_format('d/m/Y', $dates[0]);
					$from = date_format($converted_start_date, 'Y-m-d');
					$converted_end_date = date_create_from_format('d/m/Y', $dates[1]);
					$to = date_format($converted_end_date, 'Y-m-d');

					$query->whereDate('racks.created_at', '>=', $from)
						->whereDate('racks.created_at', '<=', $to);
				}
			})
			->where(function ($query) use ($request) {
				if (!empty($request->today)) {
					$today = date("Y-m-d", strtotime($request->today));
					$query->whereDate('racks.created_at', '=', $today);
				}
			})
    		->groupBy('racks.id')
			->get();
		// dd($rack_list);
		return datatables::of($rack_list)
			->addColumn('status', function ($rack_list) {
				$status = $rack_list->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $rack_list->status;
			})
			->addColumn('action', function ($rack_list) use ($request) {
				$img1 = asset('public/theme/img/table/edit.svg');
				$img3 = asset('public/theme/img/table/view.svg');
				$view_url = '';
				
				$view_url = '<a href="#!/part-pkg/rack/view/' . $rack_list->id . '" class=""><img src="' . $img3 . '" alt="View" class="img-responsive"></a>';

				$edit_url = '<a href="#!/part-pkg/rack/form/' . $rack_list->id . '"><img src="' . $img1 . '" alt="Edit" class="img-responsive"></a>';

				$actions = $edit_url.$view_url;

				return $actions;
			})->make(true);
    }
    public function getRackFormDetails(Request $request){
    	$action = 'Add';
    	$rack_details = [];
    	if($request->id){
    		$action = 'Edit';
    		$rack_details = Rack::select(
    			'racks.*',
    			DB::raw('IF(racks.deleted_at IS NULL, "Active","Inactive") as status'),
    			'configs.name as type_name'
    		)
    		->leftJoin('configs','configs.id','racks.type_id')
    		->where('racks.id',$request->id)
    		->first();
    	}
    	$this->data['action'] = $action;
    	$this->data['rack_details'] = $rack_details;
    	$this->data['type_list'] = collect(
    		Config::select('name', 'id')
    			->where('config_type_id', 134)
    			->get()
    		)->prepend(['id' => '', 'name' => 'Select Type']);
    	return response()->json($this->data);
    }
    public function saveRack(Request $request){
    	// dd($request->all());
    	try {
			$error_messages = [
				'type_id.required' => 'Type is Required',
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
			];
			$validator = Validator::make($request->all(), [
				'type_id' => 'required',
				'name' => [
					'required:true',
					'unique:racks,name,' . $request->id . ',id,type_id,' . $request->type_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}
			DB::beginTransaction();
			if (!$request->id) {
				$rack = new Rack;
				$rack->created_by = Auth::user()->id;
				$rack->created_at = Carbon::now();
			} else {
				$rack = Rack::find($request->id);
				$rack->updated_by = Auth::user()->id;
				$rack->updated_at = Carbon::now();
			}
			$rack->company_id = Auth::user()->company_id;
			$rack->outlet_id = Auth::user()->working_outlet_id;
			$rack->type_id = $request->type_id;
			$rack->name = $request->name;
			if ($request->status == 'Inactive') {
				$rack->deleted_at = Carbon::now();
				$rack->deleted_by = Auth::user()->id;
			} else {
				$rack->deleted_at = NULL;
				$rack->deleted_by = NULL;
			}
			$rack->save();
			DB::commit();
			if (!($request->id)) {
				return response()->json(['success' => true, 'message' => ['Rack Added Successfully']]);
			} else {
				return response()->json(['success' => true, 'message' => ['Rack Updated Successfully']]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
    }
}
