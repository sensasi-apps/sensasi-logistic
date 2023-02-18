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

			$eloquentCollection = $queryBuilder->get();

			if (isset($params['append'])) {
				foreach ($params['append'] as $key => $append) {
					if (is_numeric($key)) {
						$eloquentCollection->append($append);
					} else {
						$eloquentCollection->each(function ($item) use ($key, $append) {
							$item->$key->append($append);
						});
					}
				}
			}

			return DataTables::of($eloquentCollection)->make();
		}
	}
}
