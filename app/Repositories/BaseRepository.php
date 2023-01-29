<?php

namespace App\Repositories;

use Illuminate\Validation\ValidationException;

class BaseRepository
{
	private array $errors = [];

	/**
	 * Throw error if any
	 *
	 * @return void
	 * @throws \Exception
	 */
	protected function throwErrorIfAny(): void
	{
		if ($this->getErrors()) {
			throw ValidationException::withMessages($this->getErrors());
		}
	}

	/**
	 * Add error
	 *
	 * @param string $error
	 * @return void
	 */
	protected function addError(string $error): void
	{
		$this->errors[] = $error;
	}

	/**
	 * Get errors
	 *
	 * @return array
	 */
	protected function getErrors(): array
	{
		return $this->errors;
	}

	protected function resetErrors()
	{
		$this->errors = [];
	}
}
