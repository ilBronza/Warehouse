<?php

namespace IlBronza\Warehouse\Models\Room;

use IlBronza\Warehouse\Models\BaseSpaceModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class RoomCategory extends BaseSpaceModel
{
	static $modelConfigPrefix = 'roomCategory';
	protected $fillable = ['name', 'description'];
	protected array $dependentRelations = ['roomTypes'];

	public function roomTypes() : HasMany
	{
		return $this->hasMany(RoomType::getProjectClassName(), 'room_category_id');
	}

	public function rooms() : HasManyThrough
	{
		return $this->hasManyThrough(
			Room::getProjectClassName(), RoomType::getProjectClassName(),
			'room_category_id', 'room_type_id'
		);
	}
}
