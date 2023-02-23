<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class Datatable2Controller extends Controller
{
	public function __invoke(string $modelName, string $params_json)
	{
		if (request()->ajax()) {
			$modelClass = "App\Models\\$modelName";
			$params = json_decode($params_json, true);

			$queryBuilder = new $modelClass;

			if (isset($params['with'])) {
				$queryBuilder = $queryBuilder->with($params['with']);
			}

			if (!request()->order) {
				$queryBuilder->orderByDesc('id');
			}

			// TODO: fix search by date is reversed like 2020-01-01 to 01-01-2020
			$tmp = DataTables::eloquent($queryBuilder);

			if (isset($params['appends'])) {
				$tmp->addColumns($params['appends']);
			}

			return $tmp->make();
		}
	}
}
