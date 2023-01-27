<?php

namespace App\Repositories;

class BaseRepository
{
	protected array $errors = [];

	/**
	 * Throw error if any
	 *
	 * @return void
	 * @throws \Exception
	 */
	protected function throwErrorIfAny(): void
	{
		if ($this->errors) {
			throw new \Exception(json_encode($this->errors));
		}
	}
}
