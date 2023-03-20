<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


class BaseModelService
{
	protected string $modelClass;
	protected Model $workingInstance;
	protected array $withs = [];

	public function __construct(Model $model = null)
	{
		$this->workingInstance = $model ?? $this->retrieveModel();
	}

	private function retrieveModel(): Model
	{
		if (Route::current() == null) {
			return new $this->modelClass;
		}

		$id = Route::current()->parameters[Str::snake(class_basename($this->modelClass))] ?? null;

		if ($id === null) {
			return new $this->modelClass;
		}

		return $this->modelClass::with($this->withs)->findOrFail($id);
	}
}
