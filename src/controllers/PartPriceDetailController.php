<?php

namespace Abs\PartPkg;

use App\Part;
use Abs\PartPkg\PartPricingDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Carbon\Carbon;
use Validator;
use Yajra\Datatables\Datatables;

class PartPriceDetailController extends Controller
{
    public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}
    public function getPartPriceDetailList(Request $request) {
		$part_price_details = PartPricingDetail::withTrashed()
			->select(
				'part_pricing_details.id',
				'parts.name',
				'parts.code',
				DB::raw('format(ROUND(part_pricing_details.regular_price),2,"en_IN") as regular_price'),
				DB::raw('format(ROUND(part_pricing_details.retail_price),2,"en_IN") as retail_price'),
				DB::raw('COALESCE(DATE_FORMAT(part_pricing_details.effective_from,"%d-%m-%Y"), "--") as effective_from'),
				DB::raw('COALESCE(DATE_FORMAT(part_pricing_details.effective_to,"%d-%m-%Y"), "--") as effective_to')
			)
			->leftjoin('parts', 'parts.id', 'part_pricing_details.part_id')
			->where('parts.company_id', Auth::user()->company_id)
			->where(function ($query) use ($request) {

				if (!empty($request->date_range)) {

					$dates = explode(' - ', $request->date_range);
					$converted_start_date = date_create_from_format('d/m/Y', $dates[0]);
					$from = date_format($converted_start_date, 'Y-m-d');
					$converted_end_date = date_create_from_format('d/m/Y', $dates[1]);
					$to = date_format($converted_end_date, 'Y-m-d');

					$query->whereDate('part_pricing_details.created_at', '>=', $from)
						->whereDate('part_pricing_details.created_at', '<=', $to);
				}
			})
			->where(function ($query) use ($request) {
				if (!empty($request->today)) {
					$today = date("Y-m-d", strtotime($request->today));
					$query->whereDate('part_pricing_details.created_at', '=', $today);

				}
			})
			->orderBy('part_pricing_details.id','desc');
		return Datatables::of($part_price_details)
			->addColumn('action', function ($part_price_details) {
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$output = '';
				$output .= '<a href="#!/part-pkg/part-price-detail/edit/' . $part_price_details->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';
				return $output;
			})
			->make(true);
	}
    public function getPartPriceDetailFormData(Request $request) {
		$id = $request->id;
		if (!$id) {
			$action = 'Add';
		} else {
			$part_price_detail = PartPricingDetail::select(
                    'part_pricing_details.id',
					DB::raw('CONCAT(parts.code," / ",parts.name) AS code'),
					'part_pricing_details.part_id as part_id',
                    'part_pricing_details.regular_price as regular_price',
                    'part_pricing_details.retail_price as retail_price',
					DB::raw('DATE_FORMAT(part_pricing_details.effective_from,"%d-%m-%Y") as effective_from'),
                    DB::raw('DATE_FORMAT(part_pricing_details.effective_to,"%d-%m-%Y") as effective_to')
                )
                ->leftjoin('parts', 'parts.id', 'part_pricing_details.part_id')->where('part_pricing_details.id',$id)->first();
			$action = 'Edit';
            $this->data['part_price_detail'] = $part_price_detail;
		}
		$this->data['action'] = $action;
		return response()->json($this->data);
	}
    public function getPart(Request $request){
        if (!empty($request->key)) {
            $part_details = Part::select('id','code','name')
			->where('company_id', Auth::user()->company_id)
            ->where('code','like','%'.$request->key.'%')->get();
        } else {
			$part_details = [];
		}
		return response()->json(['part_details' => $part_details]);
    }
    public function savePartPriceDetail(Request $request){
        try {
            $error_messages = [
				'part_id.required' => 'Part Code is Required',
                'regular_price.required' => 'Regular Price is Required',
                'retail_price.required' => 'Retail Price is Required',
			];
			$validator = Validator::make($request->all(), [
				'part_id' => 'required',
                'regular_price' => 'required',
                'retail_price' => 'required',
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}
            
            DB::beginTransaction();
            if($request->id != null) {
                $part_price_details = PartPricingDetail::where('id',$request->id)->first();
                $part_price_details->updated_by_id = Auth::user()->id;
                $part_price_details->updated_at = Carbon::now();
            }else{
                $part_price_details = new PartPricingDetail;
                $part_price_details->created_by_id = Auth::user()->id;
                $part_price_details->created_at = Carbon::now();
            }
            $part_price_details->part_id = $request->part_id;
            $part_price_details->regular_price = $request->regular_price;
            $part_price_details->retail_price = $request->retail_price;
            $part_price_details->effective_from = $request->effective_from ? date('Y-m-d',strtotime($request->effective_from)) : null;
            $part_price_details->effective_to = $request->effective_to ? date('Y-m-d',strtotime($request->effective_to)) : null;
            $part_price_details->save();
            DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Part Price Detail Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Part Price Detail Updated Successfully',
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
}
