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
				return DataTables::of($modelClass::with($request->with))->make();
			} else {
				return DataTables::of($modelClass::query())->make();
			}
		}
	}
}
