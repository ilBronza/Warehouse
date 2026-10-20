<?php

namespace IlBronza\Warehouse\Models\Structure;

use IlBronza\Warehouse\Models\BaseSpaceModel;
use IlBronza\Warehouse\Models\Room\Room;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Structure extends BaseSpaceModel
{
	static $modelConfigPrefix = 'structure';
	protected $fillable = ['name', 'description'];
	protected array $dependentRelations = ['rooms'];

	public function rooms() : HasMany
	{
		return $this->hasMany(Room::getProjectClassName(), 'structure_id');
	}
}
