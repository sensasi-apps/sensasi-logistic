<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use DateTime;
use Yajra\DataTables\Facades\DataTables;

class DatatableController extends Controller
{
	public function __invoke(string $modelName, string $params_json = null): null|JsonResponse
	{
		if (request()->ajax()) {
			$modelClass = "App\Models\\$modelName";
			$params = json_decode($params_json, true);

			$queryBuilder = $modelClass::with($params['withs'] ?? []);

			$search = request()->search;

			if (isset($search['value']) && $search['value'] = $this->reformatDate($search['value'])) {
				request()->merge(['search' => $search]);
			}

			$tmp = DataTables::eloquent($queryBuilder);

			if (isset($params['appends'])) {
				$tmp->addColumns($params['appends']);
			}

			return $tmp->make();
		}
	}

	private function reformatDate(string $date): string|null
	{
		$format = 'd-m';
		$d = DateTime::createFromFormat($format, $date);
		if ($d && $d->format($format) === $date) {
			return $d->format('m-d');
		}

		$format = 'm-Y';
		$d = DateTime::createFromFormat($format, $date);
		if ($d && $d->format($format) === $date) {
			return $d->format('Y-m');
		}

		$format = 'd-m-Y';
		$d = DateTime::createFromFormat($format, $date);
		if ($d && $d->format($format) === $date) {
			return $d->format('Y-m-d');
		}

		return null;
	}
}
