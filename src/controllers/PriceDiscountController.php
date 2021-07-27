<?php

namespace Abs\PartPkg;

use App\Config;
use App\Region;
use Abs\PartPkg\DiscountGroup;
use Abs\PartPkg\PriceDiscount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Validator;
use Yajra\Datatables\Datatables;

class PriceDiscountController extends Controller
{
	public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}
	//List
    public function getList(Request $request) {
		$price_discounts = PriceDiscount::withTrashed()
			->select([
				'price_discounts.id',
				'regions.name as region',
				'discount_groups.name as discount_group',
				'price_discounts.purchase_discount',
				'price_discounts.customer_discount',
				DB::raw('IF(price_discounts.deleted_at IS NULL, "Active","Inactive") as status'),
				DB::raw('IF(price_discounts.effective_from IS NULL, "--",DATE_FORMAT(price_discounts.effective_from,"%d-%m-%Y")) as effective_from'),
                DB::raw('IF(price_discounts.effective_to IS NULL, "--",DATE_FORMAT(price_discounts.effective_to,"%d-%m-%Y")) as effective_to'),
			])
			->leftjoin('regions', 'regions.id', 'price_discounts.region_id')
			->leftjoin('discount_groups', 'discount_groups.id', 'price_discounts.discount_group_id')
			->where('price_discounts.company_id', Auth::user()->company_id)
			//Filter
			->where(function ($query) use ($request) {
				if (!empty($request->region_id)) {
					$query->where('price_discounts.region_id', $request->region_id);
				}
			})
			->where(function ($query) use ($request) {
				if (!empty($request->discount_grp_id)) {
					$query->where('price_discounts.discount_group_id', $request->discount_grp_id);
				}
			})
			->where(function ($query) use ($request) {
				if ($request->status_list_filter_id == '1') {
					$query->whereNull('price_discounts.deleted_at');
				} else if ($request->status_list_filter_id == '0') {
					$query->whereNotNull('price_discounts.deleted_at');
				}
			})
			->orderBy('price_discounts.id','DESC');


		return Datatables::of($price_discounts)
			->addColumn('status', function ($price_discount) {
				$status = $price_discount->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $price_discount->status;
			})
			->addColumn('action', function ($price_discount) {

				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img3 = asset('public/theme/img/table/view.svg');

				$output = '';

				$output .= '<a href="#!/part-pkg/price-discount/form/' . $price_discount->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';

				$output .= '<a href="#!/part-pkg/price-discount/view/' . $price_discount->id . '" id = "" title="View"><img src="' . $img3 . '" alt="View" class="img-responsive" onmouseover=this.src="' . $img3 . '" onmouseout=this.src="' . $img3 . '"></a>';
			
				$output .= '<a href="javascript:;" data-toggle="modal" data-target="#price-discount-delete-modal" onclick="angular.element(this).scope().deletePriceDiscount(' . $price_discount->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';

				return $output;

			})
			->make(true);
	}
	//Get Form Data
	public function getFormData(Request $request){
		// dd($request->all());
		$action = 'Add';
    	$price_discount = [];
    	if($request->id){
    		$action = 'Edit';
    		$price_discount = PriceDiscount::select(
    				'price_discounts.*',
    				'regions.name as region_name',
    				'discount_groups.name as discount_group_name',
                DB::raw('DATE_FORMAT(price_discounts.effective_from,"%d-%m-%Y") as effective_from'),
                DB::raw('DATE_FORMAT(price_discounts.effective_to,"%d-%m-%Y") as effective_to')
    			)->withTrashed()
    			->leftjoin('regions','regions.id','price_discounts.region_id')
    			->leftjoin('discount_groups','discount_groups.id','price_discounts.discount_group_id')
    			->where('price_discounts.id',$request->id)->first();
    	}
    	$this->data['region_list'] = collect(Region::select('name', 'id')->get())->prepend(['id' => '', 'name' => 'Select Region']);
    	$this->data['discount_grp_list'] = collect(DiscountGroup::select('name', 'id')->get())->prepend(['id' => '', 'name' => 'Select Discount Group']);
    	if(isset($request->type) && $request->type == 'view')
    		$action = 'View';
    	$this->data['action'] = $action;
    	$this->data['price_discount'] = $price_discount;
    	return response()->json($this->data);
	}
	//Save data
	public function saveData(Request $request){
		// dd($request->all());
		try {
			$error_messages = [
				'region_id.required' => 'Region is Required',
				'discount_group_id.required' => 'Discount Group is Required',
				'purchase_discount.required' => 'Purchase Discount is Required',
				'approved_discount.required' => 'Approved Discount is Required',
				'customer_discount.required' => 'Customer Discount is Required',
			];
			$validator = Validator::make($request->all(), [
				'region_id' => 'required',
				'purchase_discount' => 'required',
				'approved_discount' => 'required',
				'customer_discount' => 'required',
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}
			DB::beginTransaction();
            $price_discount_check = PriceDiscount::withTrashed()->where('region_id',$request->region_id)->whereNull('deleted_at')->where('discount_group_id',$request->discount_group_id);
            if ($request->id){
                $price_discount_check=$price_discount_check->where('id','!=',$request->id);
            }
            $price_discount_check=$price_discount_check->pluck('id')->first();
            if ($price_discount_check) {
                return response()->json(['success' => false, 'errors' => ['Discount for same region is is already taken']]);
            }
            if (!$request->id) {
				$price_discount = new PriceDiscount;
				$price_discount->company_id = Auth::user()->company_id;
				$price_discount->created_by_id = Auth::user()->id;
			} else {
				$price_discount = PriceDiscount::withTrashed()->find($request->id);
				$price_discount->updated_by_id = Auth::user()->id;
			}
			$price_discount->region_id = $request->region_id;
			$price_discount->discount_group_id = $request->discount_group_id;
			$price_discount->purchase_discount = $request->purchase_discount;
			$price_discount->approved_discount = $request->approved_discount;
			$price_discount->customer_discount = $request->customer_discount;
			if ($request->status == 'Inactive') {
				$price_discount->deleted_at = Carbon::now();
				$price_discount->deleted_by_id = Auth::user()->id;
			} else {
				$price_discount->deleted_at = NULL;
				$price_discount->deleted_by_id = NULL;
			}
            $price_discount->effective_from = date('Y-m-d',strtotime($request->effective_from));
            $price_discount->effective_to = date('Y-m-d',strtotime($request->effective_to));
			$price_discount->save();
			DB::commit();
			if (!($request->id)) {
				return response()->json(['success' => true, 'message' => ['Price Discount Added Successfully']]);
			} else {
				return response()->json(['success' => true, 'message' => ['Price Discount Updated Successfully']]);
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
			$price_discount = PriceDiscount::withTrashed()->where('id', $request->id)->first();
            $price_discount->deleted_by_id=Auth::user()->id;
            $price_discount->deleted_at=Carbon::now();
            $price_discount->save();
			if ($price_discount) {
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Price Discount Deleted Successfully']);
			}
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}

	// Filter
	public function getFilterData() {

		$this->data['region_list'] = collect(
				Region::select('name', 'id')
					->get()
				)->prepend(['id' => '', 'name' => 'Select Region']);
		$this->data['discount_grp_list'] = collect(
				DiscountGroup::select('name', 'id')
					->get()
				)->prepend(['id' => '', 'name' => 'Select Discount Group']);
		$this->data['status_list'] = [
				['id' => '', 'name' => 'Select Status'],
				['id' => '1', 'name' => 'Active'],
				['id' => '0', 'name' => 'Inactive'],
			];

		return response()->json($this->data);
	}
}
