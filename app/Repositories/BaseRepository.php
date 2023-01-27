<?php

namespace App\Repositories;

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
			throw new \Exception(json_encode($this->errors));
		}
	}

	/**
	 * Add error
	 *
	 * @param string $error
	 * @return void
	 */
	protected function addError(string|array $error): void
	{
		if (is_array($error)) {
			$this->errors = array_merge($this->errors, $error);
			return;
		}

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
}
