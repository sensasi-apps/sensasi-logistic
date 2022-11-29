<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;


class DatatableController extends Controller
{
	public function __invoke(Request $request, $modelName)
	{
		if (request()->ajax()) {
			$modelClass = "App\Models\\$modelName";
			
			if ($request->with) {
				$withs = explode(',', $request->with);
				$queryBuilder = $modelClass::with($withs);
			} else {
				$queryBuilder = $modelClass::query();
			}

			if (!$request->order) {
				$queryBuilder->orderByDesc('id');
			}
			
			return DataTables::eloquent($queryBuilder)->make();
		}
	}
}
