<?php

namespace Abs\PartPkg;

use App\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Validator;
use Yajra\Datatables\Datatables;

class ComponentController extends Controller
{
    public function getComponentList(Request $request){
    	// dd($request->all());
    	$component_list = Config::select(
				'id',
				'name'
			)
			->groupBy('id')
			->where('config_type_id',133)
			->get();
		// dd($component_list);
		return datatables::of($component_list)
			->addColumn('action', function ($component_list) use ($request) {
				$img1 = asset('public/theme/img/table/edit.svg');
				$img3 = asset('public/theme/img/table/view.svg');
				$view_url = '';
				
				$view_url = '<a href="#!/part-pkg/component/view/' . $component_list->id . '" class=""><img src="' . $img3 . '" alt="View" class="img-responsive"></a>';

				$edit_url = '<a href="#!/part-pkg/component/form/' . $component_list->id . '"><img src="' . $img1 . '" alt="Edit" class="img-responsive"></a>';

				$actions = $edit_url.$view_url;

				return $actions;
			})->make(true);
    }
    public function getComponentFormDetails(Request $request){
    	$action = 'Add';
    	$component_details = [];
    	if($request->id){
    		$action = 'Edit';
    		$component_details = Config::where('id',$request->id)->first();
    	}
    	$this->data['action'] = $action;
    	$this->data['component_details'] = $component_details;
    	return response()->json($this->data);
    }
    public function saveComponent(Request $request){
    	// dd($request->all());
    	try {
			$error_messages = [
				'name.required' => 'Component Name is Required',
				'name.unique' => 'Component Name is already taken',
			];
			$validator = Validator::make($request->all(), [
				'name' => [
					'required:true',
					'unique:configs,name,' . $request->id . ',id',
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}
			DB::beginTransaction();
			if (!$request->id) {
				$component = new Config;
				$component->config_type_id = 133;
			} else {
				$component = Config::find($request->id);
			}
			$component->name = $request->name;
			$component->save();
			DB::commit();
			if (!($request->id)) {
				return response()->json(['success' => true, 'message' => ['Component Added Successfully']]);
			} else {
				return response()->json(['success' => true, 'message' => ['Component Updated Successfully']]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
    }
}
