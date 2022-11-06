<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DatatableController extends Controller
{
	public function __invoke($modelName)
	{
		if (request()->ajax()) {
			$modelClass = "App\Models\\$modelName";
			return DataTables::of($modelClass::query())->make();
		}
	}
}
