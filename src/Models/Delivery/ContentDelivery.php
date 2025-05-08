<?php

namespace IlBronza\Warehouse\Models\Delivery;

use IlBronza\Clients\Models\Client;
use IlBronza\Clients\Models\Destination;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

use function is_string;

class ContentDelivery extends MorphPivot
{
	use CRUDUseUuidTrait;
	use PackagedModelsTrait;

	public $fillable = [
		'quantity_required',
		'partial',
	];

	static $deletingRelationships = [];

	static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'contentDelivery';
	protected $keyType = 'string';

	public function scopeByDelivery($query, string|Delivery $delivery)
	{
		if(! is_string($delivery))
			$delivery = $delivery->getKey();

		$query->where('delivery_id', $delivery);
	}

	public function unitloads()
	{
		return $this->hasMany(Unitload::gpc(), 'content_delivery_id');
	}

	public function content()
	{
		return $this->morphTo('content');
	}

	public function getClientAttribute() : ? Client
	{
		return $this->getClient();
	}

	public function getClient() : ? Client
	{
		return $this->content?->getClient();
	}

	public function getDestinationAttribute() : ? Destination
	{
		return $this->getDestination();
	}

	public function getDestination() : ? Destination
	{
		return $this->content?->getDestination();
	}
}