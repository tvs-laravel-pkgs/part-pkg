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

class VariantController extends Controller
{
    public function getVariantList(Request $request){
    	// dd($request->all());
    	$variant_list = Config::select(
				'id',
				'name'
			)
			->groupBy('id')
			->where('config_type_id',132)
			->get();
		// dd($variant_list);
		return datatables::of($variant_list)
			->addColumn('action', function ($variant_list) use ($request) {
				$img1 = asset('public/theme/img/table/edit.svg');
				$img3 = asset('public/theme/img/table/view.svg');
				$view_url = '';
				
				$view_url = '<a href="#!/part-pkg/variant/view/' . $variant_list->id . '" class=""><img src="' . $img3 . '" alt="View" class="img-responsive"></a>';

				$edit_url = '<a href="#!/part-pkg/variant/form/' . $variant_list->id . '"><img src="' . $img1 . '" alt="Edit" class="img-responsive"></a>';

				$actions = $edit_url.$view_url;

				return $actions;
			})->make(true);
    }
    public function getVariantFormDetails(Request $request){
    	$action = 'Add';
    	$variant_details = [];
    	if($request->id){
    		$action = 'Edit';
    		$variant_details = Config::where('id',$request->id)->first();
    	}
    	$this->data['action'] = $action;
    	$this->data['variant_details'] = $variant_details;
    	return response()->json($this->data);
    }
    public function saveVariant(Request $request){
    	// dd($request->all());
    	try {
			$error_messages = [
				'name.required' => 'Variant Name is Required',
				'name.unique' => 'Variant Name is already taken',
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
				$variant = new Config;
				$variant->config_type_id = 132;
			} else {
				$variant = Config::find($request->id);
			}
			$variant->name = $request->name;
			$variant->save();
			DB::commit();
			if (!($request->id)) {
				return response()->json(['success' => true, 'message' => ['Variant Added Successfully']]);
			} else {
				return response()->json(['success' => true, 'message' => ['Variant Updated Successfully']]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
    }
}
