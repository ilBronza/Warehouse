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

class ContentDelivery extends MorphPivot
{
	use CRUDUseUuidTrait;
	use CRUDModelTrait;

	use PackagedModelsTrait {
		PackagedModelsTrait::getRouteBaseNamePrefix insteadof CRUDModelTrait;
		PackagedModelsTrait::getPluralTranslatedClassname insteadof CRUDModelTrait;
		PackagedModelsTrait::getTranslatedClassname insteadof CRUDModelTrait;
	}

	public $fillable = [
		'quantity_required',
		'partial',
	];

	protected $casts = [
		'loaded_at' => 'datetime',
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

	public function delivery()
	{
		return $this->belongsTo(Delivery::gpc());
	}

	public function getDelivery() : ? Delivery
	{
		return $this->delivery;
	}

	public function unitloads()
	{
		return $this->hasMany(Unitload::gpc(), 'content_delivery_id');
	}

	public function content()
	{
		return $this->morphTo('content');
	}

	public function getOrderAttribute() : ? Order
	{
		return $this->getContent()?->getOrder();
	}

	public function getUnitloads() : Collection
	{
		return $this->unitloads;
	}

	public function getContent() : ? DeliverableInterface
	{
		return $this->content;
	}

	public function getClientAttribute() : ? Client
	{
		return $this->getClient();
	}

	public function getClient() : ? Client
	{
		return $this->getContent()?->getClient();
	}

	public function getDestinationAttribute() : ? Destination
	{
		return $this->getDestination();
	}

	public function getDestination() : ? Destination
	{
		return $this->content?->getDestination();
	}

	public function scopeByContent($query, DeliverableInterface $content)
	{
		return $query->where('content_type', $content->getMorphClass())
			->where('content_id', $content->getKey());
	}

	public function getQuantityRequired() : ? float
	{
		return $this->quantity_required;
	}

	public function getVolumeMc() : float
	{
		return $this->volume_mc;
	}

	public function getVolumeMcAttribute() : float
	{
		return $this->getUnitloads()->sum('volume_mc');
	}

	protected static function booted(): void
	{
		static::deleting(function (ContentDelivery $contentDelivery) {
			if($contentDelivery->unitloads()->count())
				DeliveryDetacherHelper::detachContentDelivery($contentDelivery);
		});
	}

	public function getDetachUrl() : string
	{
		return $this->getKeyedRoute('detach');
	}

	public function isLoaded() : bool
	{
		return !! $this->getLoadedAt();
	}

	public function getLoadedAt() : ? Carbon
	{
		return $this->loaded_at;
	}

	public function getCumulativeLoadingUrl() : ? string
	{
		return $this->getKeyedRoute('loadCumulative');
	}

	public function getCumulativeUnLoadingUrl() : ? string
	{
		return $this->getKeyedRoute('unloadCumulative');
	}

	public function getLoadingUrl() : ? string
	{
		return $this->getKeyedRoute('load');
	}

	public function getUnLoadingUrl() : ? string
	{
		return $this->getKeyedRoute('unload');
	}

	public function getPopupUrl() : string
	{
		return $this->getKeyedRoute('popup');
	}
}