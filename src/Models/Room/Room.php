<?php

namespace IlBronza\Warehouse\Models\Room;

use IlBronza\Clients\Models\Destination;
use IlBronza\Warehouse\Models\BaseSpaceModel;
use IlBronza\Warehouse\Models\Structure\Structure;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends BaseSpaceModel
{
	static $modelConfigPrefix = 'room';
	protected $fillable = [
		'name', 'code', 'floor', 'surface_sqm', 'notes',
		'structure_id', 'room_type_id', 'destination_id',
	];
	protected $casts = ['surface_sqm' => 'decimal:2'];

	public function structure() : BelongsTo
	{
		return $this->belongsTo(Structure::getProjectClassName(), 'structure_id');
	}

	public function roomType() : BelongsTo
	{
		return $this->belongsTo(RoomType::getProjectClassName(), 'room_type_id');
	}

	public function destination() : BelongsTo
	{
		return $this->belongsTo(Destination::getProjectClassName(), 'destination_id');
	}

	public function getDestination() : ?Destination
	{
		return $this->destination;
	}
}
