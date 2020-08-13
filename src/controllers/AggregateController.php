<?php

namespace Abs\PartPkg;

use Abs\PartPkg\Aggregate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Validator;
use Yajra\Datatables\Datatables;

class AggregateController extends Controller
{
    public function getAggregateList(Request $request){
    	// dd($request->all());
    	$aggregate_list = Aggregate::select(
				'aggregates.id as aggregates_id',
				'aggregates.code',
				'aggregates.name',
				DB::raw('IF(aggregates.deleted_at IS NULL, "Active","Inactive") as status')
			)
			->groupBy('aggregates.id')
			->get();
		// dd($aggregate_list);
		return datatables::of($aggregate_list)
			->addColumn('status', function ($aggregate_list) {
				$status = $aggregate_list->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $aggregate_list->status;
			})
			->addColumn('action', function ($aggregate_list) use ($request) {
				$img1 = asset('public/theme/img/table/edit.svg');
				$img3 = asset('public/theme/img/table/view.svg');
				$view_url = '';
				
				$view_url = '<a href="#!/part-pkg/aggregate/view/' . $aggregate_list->aggregates_id . '" class=""><img src="' . $img3 . '" alt="View" class="img-responsive"></a>';

				$edit_url = '<a href="#!/part-pkg/aggregate/form/' . $aggregate_list->aggregates_id . '"><img src="' . $img1 . '" alt="Edit" class="img-responsive"></a>';

				$actions = $edit_url.$view_url;

				return $actions;
			})->make(true);
    }
    public function getAggregateFormDetails(Request $request){
    	$action = 'Add';
    	$aggregate_details = [];
    	if($request->id){
    		$action = 'Edit';
    		$aggregate_details = Aggregate::select(
    			'aggregates.*',
    			DB::raw('IF(aggregates.deleted_at IS NULL, "Active","Inactive") as status')
    		)
    		->where('id',$request->id)->first();
    	}
    	$this->data['action'] = $action;
    	$this->data['aggregate_details'] = $aggregate_details;
    	return response()->json($this->data);
    }
    public function saveAggregate(Request $request){
    	// dd($request->all());
    	try {
			$error_messages = [
				'code.required' => 'Aggregate Code is Required',
				'code.unique' => 'Aggregate Code is already taken',
				'name.required' => 'Aggregate Name is Required',
				'name.unique' => 'Aggregate Name is already taken',
			];
			$validator = Validator::make($request->all(), [
				'code' => [
					'required:true',
					'unique:aggregates,code,' . $request->id . ',id',
				],
				'name' => [
					'required:true',
					'unique:aggregates,name,' . $request->id . ',id',
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$aggregate = new Aggregate;
				$aggregate->created_by = Auth::user()->id;
				$aggregate->created_at = Carbon::now();
				$aggregate->updated_at = NULL;
			} else {
				$aggregate = Aggregate::find($request->id);
				$aggregate->updated_by = Auth::user()->id;
				$aggregate->updated_at = Carbon::now();
			}
			$aggregate->code = $request->code;
			$aggregate->name = $request->name;
			if ($request->status == 'Inactive') {
				$aggregate->deleted_at = Carbon::now();
				$aggregate->deleted_by = Auth::user()->id;
			} else {
				$aggregate->deleted_at = NULL;
				$aggregate->deleted_by = NULL;
			}
			$aggregate->save();
			DB::commit();
			if (!($request->id)) {
				return response()->json(['success' => true, 'message' => ['Aggregate Added Successfully']]);
			} else {
				return response()->json(['success' => true, 'message' => ['Aggregate Updated Successfully']]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
    }
}
