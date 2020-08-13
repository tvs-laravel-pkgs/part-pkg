<?php

namespace Abs\PartPkg;

use Abs\PartPkg\SubAggregate;
use Abs\PartPkg\Aggregate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Validator;
use Yajra\Datatables\Datatables;

class SubAggregateController extends Controller
{
    public function getSubAggregateList(Request $request){
    	// dd($request->all());
    	$sub_aggregate_list = SubAggregate::select(
				'sub_aggregates.id',
				'sub_aggregates.code',
				'sub_aggregates.name',
				DB::raw('IF(sub_aggregates.deleted_at IS NULL, "Active","Inactive") as status')
			)
			->groupBy('sub_aggregates.id')
			->get();
		return datatables::of($sub_aggregate_list)
			->addColumn('status', function ($sub_aggregate_list) {
				$status = $sub_aggregate_list->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $sub_aggregate_list->status;
			})
			->addColumn('action', function ($sub_aggregate_list) use ($request) {
				$img1 = asset('public/theme/img/table/edit.svg');
				$img3 = asset('public/theme/img/table/view.svg');
				$view_url = '';
				
				$view_url = '<a href="#!/part-pkg/sub-aggregate/view/' . $sub_aggregate_list->id . '" class=""><img src="' . $img3 . '" alt="View" class="img-responsive"></a>';

				$edit_url = '<a href="#!/part-pkg/sub-aggregate/form/' . $sub_aggregate_list->id . '"><img src="' . $img1 . '" alt="Edit" class="img-responsive"></a>';

				$actions = $edit_url.$view_url;

				return $actions;
			})->make(true);
    }
    public function getSubAggregateFormDetails(Request $request){
    	$action = 'Add';
    	$sub_aggregate_details = [];
    	if($request->id){
    		$action = 'Edit';
    		$sub_aggregate_details = SubAggregate::select(
    			'sub_aggregates.id',
    			'sub_aggregates.code as code',
    			'sub_aggregates.name as name',
    			'sub_aggregates.deleted_at as deleted_at',
    			'aggregates.id as aggregate_id',
    			'aggregates.name as aggregate_name',
    			DB::raw('IF(sub_aggregates.deleted_at IS NULL, "Active","Inactive") as status')
    		)->leftJoin('aggregates','aggregates.id','sub_aggregates.aggregate_id')
    		->where('sub_aggregates.id',$request->id)->first();
    	}
    	$this->data['aggregate_list'] = collect(Aggregate::select('name', 'id')->get())->prepend(['id' => '', 'name' => 'Select Aggregate']);
    	$this->data['action'] = $action;
    	$this->data['sub_aggregate_details'] = $sub_aggregate_details;
    	return response()->json($this->data);
    }
    public function saveSubAggregate(Request $request){
    	// dd($request->all());
    	try {
			$error_messages = [
				'code.required' => 'Sub Aggregate Code is Required',
				'code.unique' => 'Sub Aggregate Code is already taken',
				'name.required' => 'Sub Aggregate Name is Required',
				'name.unique' => 'Sub Aggregate Name is already taken',
				'aggregate_id.required' => 'Aggregate is Required',
			];
			$validator = Validator::make($request->all(), [
				'code' => [
					'required:true',
					'unique:sub_aggregates,code,' . $request->id . ',id',
				],
				'name' => [
					'required:true',
					'unique:sub_aggregates,name,' . $request->id . ',id',
				],
				'aggregate_id' => 'required',
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$sub_aggregate = new SubAggregate;
				$sub_aggregate->created_by = Auth::user()->id;
				$sub_aggregate->created_at = Carbon::now();
				$sub_aggregate->updated_at = NULL;
			} else {
				$sub_aggregate = SubAggregate::find($request->id);
				$sub_aggregate->updated_by = Auth::user()->id;
				$sub_aggregate->updated_at = Carbon::now();
			}
			$sub_aggregate->code = $request->code;
			$sub_aggregate->name = $request->name;
			$sub_aggregate->aggregate_id = $request->aggregate_id;
			if ($request->status == 'Inactive') {
				$sub_aggregate->deleted_at = Carbon::now();
				$sub_aggregate->deleted_by = Auth::user()->id;
			} else {
				$sub_aggregate->deleted_at = NULL;
				$sub_aggregate->deleted_by = NULL;
			}
			$sub_aggregate->save();
			DB::commit();
			if (!($request->id)) {
				return response()->json(['success' => true, 'message' => ['Sub Aggregate Added Successfully']]);
			} else {
				return response()->json(['success' => true, 'message' => ['Sub Aggregate Updated Successfully']]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
    }
}
