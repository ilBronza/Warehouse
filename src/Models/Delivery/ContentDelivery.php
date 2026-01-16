<?php

namespace IlBronza\Warehouse\Models\Delivery;

use Carbon\Carbon;
use IlBronza\CRUD\Traits\Model\CRUDCacheTrait;
use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\Clients\Models\Client;
use IlBronza\Clients\Models\Destination;
use IlBronza\Products\Models\Order;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryDetacherHelper;
use IlBronza\Warehouse\Models\Interfaces\DeliverableInterface;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use function array_merge;
use function is_string;
use function route;

class ContentDelivery extends MorphPivot
{
	protected $touches = ['delivery'];

	static $warningValues = [
		0 => 'notWarn',
		1 => 'toWarn',
		2 => 'warned'
	];

	use CRUDCacheTrait;
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
		return $this->morphTo('content')->withLiveClientId();
	}

	public function getOrderAttribute() : ? Order
	{
		return $this->getContent()?->getOrder();
	}

	public function getUnitloads() : Collection
	{
		return $this->unitloads;
	}

	public function getUnitloadsCount() : int
	{
		return $this->unitloads_count;
	}

	public function getUnitloadsCountAttribute() : int
	{
		return $this->unitloads->count();
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

	public function getWeightKgAttribute() : float
	{
		return $this->getUnitloads()->sum('weight_kg');
	}

	public function getWeightKg() : float
	{
		return $this->weight_kg;
	}

	public function scopeByContent($query, DeliverableInterface $content)
	{
		return $query->where('content_type', $content->getMorphClass())
			->where('content_id', $content->getKey());
	}

	public function scopeSortedByClient($query)
	{
		return $query->whereHas('content.order', function($_query)
		{
			$_query->orderBy('client_id');
		});
		             
	}


	public function getQuantityRequired() : ? float
	{
		return $this->quantity_required;
	}

	public function getVolumeMc() : float
	{
		return $this->volume_mc;
	}

	public function getQuantity()
	{
		return $this->getUnitloads()->filter(function($unitload)
		{
			return $unitload->isCompleted();
		})->sum('quantity');
	}

	public function getVolumeMcAttribute() : float
	{
		return $this->getUnitloads()->sum('volume_mc');
	}

	public function isCompleted() : bool
	{
		if($this->unitloads->count() == 0)
			return false;

		if($this->getQuantity() < ($this->getQuantityRequired() - ($this->getQuantityRequired() * 0.95)))
			return false;

		if($this->unitloads->firstWhere(function($unitload)
		{
			return ! $unitload->isCompleted();
		}))
			return false;

		return true;
	}

	protected static function booted(): void
	{
		static::deleting(function (ContentDelivery $contentDelivery) {
			if($contentDelivery->unitloads()->count())
				DeliveryDetacherHelper::detachContentDelivery($contentDelivery);
		});
	}

	public function isPartial() : bool
	{
		return !! $this->partial;
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

	public function getClientNameAttribute()
	{
		return $this->getContent()->getOrder()->getClient()->getName();
	}

	public function getPossibleWarningStatusesArray() : array
	{
		$result = [];

		foreach(static::$warningValues as $index => $status)
			$result[$index] = $this->getWarningStatusString($index);

		return $result;
	}

	public function getWarningStatusString($warningStatus) : string
	{
		return trans('warehouse::contentDeliveries.'  . static::$warningValues[$warningStatus]);
	}

	public function getWarningStatus() : string
	{
		return $this->getWarningStatusString($this->warned ?? 0);
	}

	public function setWarned($warningStatus, bool $save = false)
	{
		$this->warned = $warningStatus;

		if($save)
			$this->save();
	}

	public function getClientDestinationKey() : string
	{
		return cache()->remember(
			$this->cacheKey('getClientDestinationKey'),
			3600,
			function()
			{
				return Str::slug($this->getClient()?->getName() . '_' . $this->getDestination()?->getKey());
			}
		);
	}

	public function getLoadingTolerance() : float
	{
		return 0.1;
	}

	public function getName() : string
	{
		return "{$this->getcontent()->getName()} x {$this->getQuantityRequired()}";
	}

	public function getQuantityProduced() : float
	{
		if($this->relationLoaded('unitloads'))
			return $this->unitloads->sum(function($unitload)
			{
				if(! $unitload->isCompleted())
					return 0;

				return $unitload->getQuantity();
			});

		return $this->unitloads()->completed()->get()->sum(function($unitload)
		{
			return $unitload->getQuantity();
		});
	}
	
	public function hasBeenShipped() : bool
	{
		return !! $this->getDelivery()?->hasBeenShipped();
	}
}