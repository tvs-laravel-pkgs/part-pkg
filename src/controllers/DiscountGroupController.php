<?php

namespace Abs\PartPkg;

use App\Config;
use Abs\PartPkg\DiscountGroup;
use Abs\PartPkg\PriceDiscount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Validator;
use Yajra\Datatables\Datatables;

class DiscountGroupController extends Controller
{
	public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}
	//List
    public function getList(Request $request) {
		$discount_groups = DiscountGroup::withTrashed()
			->select([
				'discount_groups.id',
				'discount_groups.name',
				'discount_groups.code',
				'configs.name as type',
				DB::raw('IF(discount_groups.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->leftjoin('configs', 'configs.id', 'discount_groups.type_id')
			->where('discount_groups.company_id', Auth::user()->company_id)
			//Filter
			->where(function ($query) use ($request) {
				if (!empty($request->type_list_filter_id)) {
					$query->where('discount_groups.type_id', $request->type_list_filter_id);
				}
			})
			->where(function ($query) use ($request) {
				if ($request->status_list_filter_id == '1') {
					$query->whereNull('discount_groups.deleted_at');
				} else if ($request->status_list_filter_id == '0') {
					$query->whereNotNull('discount_groups.deleted_at');
				}
			})
			->orderBy('discount_groups.id','DESC');


		return Datatables::of($discount_groups)
			->addColumn('status', function ($discount_group) {
				$status = $discount_group->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $discount_group->status;
			})
			->addColumn('action', function ($discount_group) {

				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img3 = asset('public/theme/img/table/view.svg');

				$output = '';

				$output .= '<a href="#!/part-pkg/discount-group/form/' . $discount_group->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';

				$output .= '<a href="#!/part-pkg/discount-group/view/' . $discount_group->id . '" id = "" title="View"><img src="' . $img3 . '" alt="View" class="img-responsive" onmouseover=this.src="' . $img3 . '" onmouseout=this.src="' . $img3 . '"></a>';
			
				$output .= '<a href="javascript:;" data-toggle="modal" data-target="#discount-group-delete-modal" onclick="angular.element(this).scope().deleteDiscountGroup(' . $discount_group->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';

				return $output;

			})
			->make(true);
	}
	//Get Form Data
	public function getFormData(Request $request){
		// dd($request->all());
		$action = 'Add';
    	$discount_group = [];
    	if($request->id){
    		$action = 'Edit';
    		$discount_group = DiscountGroup::select(
    				'discount_groups.*',
    				'configs.name as type_name'
    			)->withTrashed()
    			->leftjoin('configs','configs.id','discount_groups.type_id')
    			->where('discount_groups.id',$request->id)->first();
    	}
    	$this->data['type_list'] = collect(Config::select('name', 'id')->where('config_type_id',136)->get())->prepend(['id' => '', 'name' => 'Select Type']);
    	if(isset($request->type) && $request->type == 'view')
    		$action = 'View';
    	$this->data['action'] = $action;
    	$this->data['discount_group'] = $discount_group;
    	return response()->json($this->data);
	}
	//Save data
	public function saveData(Request $request){
		// dd($request->all());
		try {
			$error_messages = [
				'type_id.required' => 'Name is Required',
				'code.required' => 'Name is Required',
				'code.unique' => 'Code is is already taken',
				'code.min' => 'Code is Minimum 3 Charachers',
				'code.max' => 'Code is Maximum 32 Charachers',
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
				'name.min' => 'Name is Minimum 3 Charachers',
				'name.max' => 'Name is Maximum 191 Charachers',
			];
			$validator = Validator::make($request->all(), [
				'type_id' => [
					'required:true',
				],
				'code' => [
					'min:1',
					'max:32',
					'required:true',
					'unique:discount_groups,code,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'name' => [
					'min:1',
					'max:32',
					'required:true',
					'unique:discount_groups,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}
			DB::beginTransaction();
			if (!$request->id) {
				$discount_group = new DiscountGroup;
				$discount_group->company_id = Auth::user()->company_id;
				$discount_group->created_by_id = Auth::user()->id;
			} else {
				$discount_group = DiscountGroup::withTrashed()->find($request->id);
				$discount_group->updated_by_id = Auth::user()->id;
			}
			$discount_group->type_id = $request->type_id;
			$discount_group->code = $request->code;
			$discount_group->name = $request->name;
			if ($request->status == 'Inactive') {
				$discount_group->deleted_at = Carbon::now();
				$discount_group->deleted_by_id = Auth::user()->id;
			} else {
				$discount_group->deleted_at = NULL;
				$discount_group->deleted_by_id = NULL;
			}
			$discount_group->save();
			DB::commit();
			if (!($request->id)) {
				return response()->json(['success' => true, 'message' => ['Discount Group Added Successfully']]);
			} else {
				return response()->json(['success' => true, 'message' => ['Discount Group Updated Successfully']]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}
	//Delete
	public function deleteData(Request $request){
		DB::beginTransaction();
		try {
			$price_discount = PriceDiscount::withTrashed()->where('discount_group_id', $request->id)->forceDelete();
			$discount_group = DiscountGroup::withTrashed()->where('id', $request->id)->forceDelete();
			if ($discount_group) {
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Discount Group Deleted Successfully']);
			}
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}

	// Filter
	public function getFilterData() {

		$this->data['type_list'] = collect(
				Config::select('name', 'id')
					->where('config_type_id', 136)
					->get()
				)->prepend(['id' => '', 'name' => 'Select Type']);
		$this->data['status_list'] = [
				['id' => '', 'name' => 'Select Status'],
				['id' => '1', 'name' => 'Active'],
				['id' => '0', 'name' => 'Inactive'],
			];

		return response()->json($this->data);
	}
}
