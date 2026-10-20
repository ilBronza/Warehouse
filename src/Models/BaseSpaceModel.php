<?php

namespace IlBronza\Warehouse\Models;

use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use Illuminate\Validation\ValidationException;

abstract class BaseSpaceModel extends BaseWarehouseModel
{
	use CRUDUseUuidTrait;

	protected $keyType = 'string';
	public $incrementing = false;
	static $deletingRelationships = [];
	protected array $dependentRelations = [];

	protected static function booted()
	{
		static::deleting(function (self $model) {
			foreach ($model->dependentRelations as $relation) {
				if ($model->{$relation}()->exists())
					throw ValidationException::withMessages([
						$relation => __('warehouse::spaces.inUse'),
					]);
			}
		});
	}
}
