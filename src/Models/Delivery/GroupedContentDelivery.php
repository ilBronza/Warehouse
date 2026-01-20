<?php

namespace IlBronza\Warehouse\Models\Delivery;

use Carbon\Carbon;
use IlBronza\Clients\Models\Client;
use IlBronza\Clients\Models\Destination;
use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\Products\Models\Order;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryDetacherHelper;
use IlBronza\Warehouse\Models\Interfaces\DeliverableInterface;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

use Illuminate\Support\Collection;

use function array_merge;
use function is_string;
use function route;

class GroupedContentDelivery extends Delivery
{
	protected $touches = ['delivery'];

	public $guarded = [];

	public function delivery()
	{
		return $this->belongsTo(Delivery::gpc());
	}

	public function getKey()
	{
		return $this->client_destination_key;
	}

	public function getDelivery() : ? Delivery
	{
		return $this->delivery;
	}

	public function getFirstContentDeliveryAttribute()
	{
		return $this->contentDeliveries->first();
	}
}