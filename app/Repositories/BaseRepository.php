<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class BaseRepository
{
	protected string $modelClass;
	protected string $urlParamName;
	protected Model $workingInstance;
	protected array $withs;

	public function __construct(Model $model = null)
	{
		$this->workingInstance = $model ?? $this->retrieveModel();
	}

	private function retrieveModel(): Model
	{
		if (Route::current() == null) {
			return new $this->modelClass;
		}

		$id = Route::current()->parameters[$this->urlParamName] ?? null;

		if ($id === null) {
			return new $this->modelClass;
		}

		return $this->modelClass::with($this->withs)->findOrFail($id);
	}

	protected function addDataIdToArray(array &$details): void
	{
		foreach ($details as &$detail) {
			$detail["{$this->urlParamName}_id"] = $this->workingInstance->id;
		}
	}
}
