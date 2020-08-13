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

class BrandController extends Controller
{
    public function getBrandList(Request $request){
    	// dd($request->all());
    	$brand_list = Config::select(
				'id',
				'name'
			)
			->groupBy('id')
			->where('config_type_id',131)
			->get();
		// dd($brand_list);
		return datatables::of($brand_list)
			->addColumn('action', function ($brand_list) use ($request) {
				$img1 = asset('public/theme/img/table/edit.svg');
				$img3 = asset('public/theme/img/table/view.svg');
				$view_url = '';
				
				$view_url = '<a href="#!/part-pkg/brand/view/' . $brand_list->id . '" class=""><img src="' . $img3 . '" alt="View" class="img-responsive"></a>';

				$edit_url = '<a href="#!/part-pkg/brand/form/' . $brand_list->id . '"><img src="' . $img1 . '" alt="Edit" class="img-responsive"></a>';

				$actions = $edit_url.$view_url;

				return $actions;
			})->make(true);
    }
    public function getBrandFormDetails(Request $request){
    	$action = 'Add';
    	$brand_details = [];
    	if($request->id){
    		$action = 'Edit';
    		$brand_details = Config::where('id',$request->id)->first();
    	}
    	$this->data['action'] = $action;
    	$this->data['brand_details'] = $brand_details;
    	return response()->json($this->data);
    }
    public function saveBrand(Request $request){
    	// dd($request->all());
    	try {
			$error_messages = [
				'name.required' => 'Brand Name is Required',
				'name.unique' => 'Brand Name is already taken',
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
				$brand = new Config;
				$brand->config_type_id = 131;
			} else {
				$brand = Config::find($request->id);
			}
			$brand->name = $request->name;
			$brand->save();
			DB::commit();
			if (!($request->id)) {
				return response()->json(['success' => true, 'message' => ['Brand Added Successfully']]);
			} else {
				return response()->json(['success' => true, 'message' => ['Brand Updated Successfully']]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
    }
}
