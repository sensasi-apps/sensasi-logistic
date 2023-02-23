<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class Datatable2Controller extends Controller
{
	public function __invoke(string $modelName, string $params_json): null|JsonResponse
	{
		if (request()->ajax()) {
			$modelClass = "App\Models\\$modelName";
			$params = json_decode($params_json, true);

			$queryBuilder = $modelClass::with($params['with'] ?? []);

			// TODO: fix search by date is reversed like 2020-01-01 to 01-01-2020
			$tmp = DataTables::eloquent($queryBuilder);

			if (isset($params['appends'])) {
				$tmp->addColumns($params['appends']);
			}

			return $tmp->make();
		}
	}
}
