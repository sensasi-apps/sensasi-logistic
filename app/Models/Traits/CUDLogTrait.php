<?php

namespace App\Models\Traits;

use Helper;

/**
 * Create, Update, Delete Log Trait
 */
trait CUDLogTrait
{
	public static function boot()
	{
		parent::boot();

		static::created(function (self $self) {
			Helper::logAction('created', $self);
		});

		static::updated(function (self $self) {
			Helper::logAction('updated', $self);
		});

		static::deleted(function (self $self) {
			Helper::logAction('deleted', $self);
		});
	}
}
