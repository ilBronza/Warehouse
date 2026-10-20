<?php

namespace IlBronza\Warehouse\Models\Room;

use IlBronza\Warehouse\Models\BaseSpaceModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends BaseSpaceModel
{
	static $modelConfigPrefix = 'roomType';
	protected $fillable = ['name', 'description', 'room_category_id'];
	protected array $dependentRelations = ['rooms'];

	public function roomCategory() : BelongsTo
	{
		return $this->belongsTo(RoomCategory::getProjectClassName(), 'room_category_id');
	}

	public function rooms() : HasMany
	{
		return $this->hasMany(Room::getProjectClassName(), 'room_type_id');
	}
}
