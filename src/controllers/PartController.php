<?php

namespace Abs\PartPkg;
use App\Http\Controllers\Controller;
use App\Part;
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

				DB::raw('IF(parts.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('parts.company_id', Auth::user()->company_id)

			->where(function ($query) use ($request) {
				if (!empty($request->name)) {
					$query->where('parts.name', 'LIKE', '%' . $request->name . '%');
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
			->rawColumns(['name', 'action'])
			->addColumn('name', function ($part) {
				$status = $part->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $part->name;
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
		if (!$id) {
			$part = new Part;
			$action = 'Add';
		} else {
			$part = Part::withTrashed()->find($id);
			$action = 'Edit';
		}
		$this->data['success'] = true;
		$this->data['part'] = $part;
		$this->data['action'] = $action;
		return response()->json($this->data);
	}

	public function savePart(Request $request) {
		// dd($request->all());
		try {
			$error_messages = [
				'code.required' => 'Short Name is Required',
				'code.unique' => 'Short Name is already taken',
				'code.min' => 'Short Name is Minimum 3 Charachers',
				'code.max' => 'Short Name is Maximum 32 Charachers',
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
				'name.min' => 'Name is Minimum 3 Charachers',
				'name.max' => 'Name is Maximum 191 Charachers',
			];
			$validator = Validator::make($request->all(), [
				'code' => [
					'required:true',
					'min:3',
					'max:32',
					'unique:parts,code,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'name' => [
					'required:true',
					'min:3',
					'max:191',
					'unique:parts,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$part = new Part;
				$part->company_id = Auth::user()->company_id;
			} else {
				$part = Part::withTrashed()->find($request->id);
			}
			$part->fill($request->all());
			if ($request->status == 'Inactive') {
				$part->deleted_at = Carbon::now();
			} else {
				$part->deleted_at = NULL;
			}
			$part->save();

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
}