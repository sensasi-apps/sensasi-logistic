<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DatatableController extends Controller
{
	public function __invoke(string $modelName, string $params_json = null): null|JsonResponse
	{
		if (request()->ajax()) {
			$modelClass = "App\Models\\$modelName";
			$params = json_decode($params_json, true);

			$queryBuilder = $modelClass::with($params['withs'] ?? []);

			// TODO: fix search by date is reversed like 2020-01-01 to 01-01-2020
			$tmp = DataTables::eloquent($queryBuilder);

			if (isset($params['appends'])) {
				$tmp->addColumns($params['appends']);
			}

			return $tmp->make();
		}
	}
}
