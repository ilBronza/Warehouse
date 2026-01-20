<?php

namespace IlBronza\Warehouse\Models\Delivery;

use Carbon\Carbon;
use IlBronza\Buttons\Button;
use IlBronza\Clients\Models\Client;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\Vehicles\Models\Vehicle;
use IlBronza\Warehouse\Models\BaseWarehouseModel;
use IlBronza\Warehouse\Models\Delivery\GroupedContentDelivery;
use IlBronza\Warehouse\Models\Traits\DeliveryMeasuresTrait;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Delivery extends BaseWarehouseModel
{
	use CRUDUseUuidTrait;

	use DeliveryMeasuresTrait;

	static $modelConfigPrefix = 'delivery';
	protected $keyType = 'string';

	protected $casts = [
		'datetime' => 'datetime',
		'shipped_at' => 'datetime',
		'loaded_at' => 'datetime',
		'deleted_at' => 'datetime'
	];

	public function scopePickables($query)
	{
		return $query->whereNull('shipped_at');
	}

	static public function getAddOrdersToDeliveryIndexUrl() : string
	{
		return app('warehouse')->route('deliveries.addOrdersIndex');
	}

	static public function getAddOrdersToDeliveryButton() : Button
	{
		$button = Button::create([
			'href' => static::getAddOrdersToDeliveryIndexUrl(),
			'text' => 'warehouse::deliveries.associateDelivery',
			'icon' => 'plus'
		]);

		$button->setAjaxTableButton('order', [
			'openIframe' => true
		]);

		$button->setPrimary();

		return $button;
	}

	public function getAddOrdersToDeliveryUrl() : string
	{
		return $this->getKeyedRoute('addOrders', [
			'delivery' => $this->getKey()
		]);
	}

	public function getAddGroupedContentDeliveriesToDeliveryUrl() : string
	{
		return $this->getKeyedRoute('addGroupedContentDeliveries', [
			'delivery' => $this->getKey()
		]);
	}

	public function getAddUnitloadsToDeliveryUrl() : string
	{
		return $this->getKeyedRoute('addUnitloads', [
			'delivery' => $this->getKey()
		]);
	}

	public function scopeCurrent($query)
	{
		$query->where('datetime', '>', Carbon::now()->startOfDay()->subDays(2));
	}

	public function contentDeliveries()
	{
		return $this->hasMany(ContentDelivery::gpc());
	}

	public function getContentDeliveriesByClient(Client $client) : Collection
	{
		return $this->contentDeliveries->filter(function ($item) use ($client) {
			return $item->getContent()?->getClientKey() == $client->getKey();
		});
	}

	public function getClientsContentDeliveriesAttribute()
	{
		return cache()->remember(
			$this->cacheKey('getClientsContentDeliveriesAttribute'),
			3600,
			function()
			{
				return $this->contentDeliveries->sortBy(function($item)
				{
					return $item->getContent()->getOrder()->getClient()->getName();
				})->values();
			}
		);
	}

	public function getGroupedClientsContentDeliveriesAttribute()
	{
		return $this->contentDeliveries->groupBy(function ($item)
		{
			return $item->getClientDestinationKey();
		})->values();
	}

	public function getGroupedClientsContentDeliveriesModels()
	{
		$result = $this->getGroupedClientsContentDeliveriesAttribute();

		$collection = collect();

		foreach($result as $_result)
		{
			$groupedContentDelivery = GroupedContentDelivery::make($this->getAttributes());

			$groupedContentDelivery->setRelation('contentDeliveries', $_result);

			$groupedContentDelivery->setRelation('client', $_result->first()->getClient());
			$groupedContentDelivery->setRelation('destination', $_result->first()->getDestination());

			foreach($_result as $__result)
				if($__result->getContent()?->getOrder()?->getDestination()?->getName() != $__result->getContent()?->getOrder()?->getClient()?->getName())
					$groupedContentDelivery->destination_alert = true;

			$groupedContentDelivery->client_destination_key = $_result->first()->getClientDestinationKey();

			$collection->push($groupedContentDelivery);
		}

		return $collection;
	}

	public function getContentDeliveries() : Collection
	{
		return $this->contentDeliveries;
	}

	public function getRelatedContentDeliveries()
	{
		return $this->contentDeliveries()->with('content.client', 'content.order.destination', 'unitloads')->get();
	}

	public function getVehicle() : ? Vehicle
	{
		return $this->vehicle;
	}

	public function unitloads()
	{
		return $this->hasManyThrough(
			Unitload::gpc(),
			ContentDelivery::gpc(),
			'delivery_id',         // Foreign key on content_deliveries table
			'content_delivery_id', // Foreign key on unitloads table
			'id',                  // Local key on deliveries table
			'id'                   // Local key on content_deliveries table
		);
	}

	public function getContents() : Collection
	{
		return $this->contentDeliveries->pluck('content');
	}

	public function getLoadingString() : string
	{
		$loaded = $this->contentDeliveries->where('loaded_at', '!=', null)->count();
		$total = $this->contentDeliveries->count();

		return "{$loaded}/{$total}";
	}

	public function getAssignedLoadingPercentageAttribute() : ? float
	{
		if(! $vehicle = $this->getVehicle())
			return null;

		if(! $loadingVolume = $vehicle->getMaximumVolumeMc())
			return null;

		if(! $assignedVolume = $this->getAssignedVolumeCubicMeters())
			return 0;

		return $assignedVolume / $loadingVolume * 100;
	}

	public function getAssignedVolumeCubicMeters() : ? float
	{
		$result = 0;

		foreach($this->unitloads as $unitload)
			$result += $unitload->getVolumeCubicMeters();

		return $result;
	}

	public function getUnitloadsByProduction($model) : Collection
	{
		if($this->relationLoaded('unitloads'))
			return $this->unitloads->filter(function($item) use($model)
			{
				return ($item->production_type == $model->getMorphClass()) && ($item->production_id == $model->getKey());
			});

		return $this->unitloads()->where('production_type', $model->getMorphClass())->where('production_id', $model->getKey())->get();
	}

	public function vehicle()
	{
		return $this->belongsTo(Vehicle::gpc());
	}

	public function getVehicleSelectPossibleValues()
	{
		return cache()->remember(
			'getVehicleSelectPossibleValues',
			3600,
			function()
			{
				return Vehicle::gpc()::pluck('name', 'id')->toArray();
			}
		);
	}

	public function getDateTimeString() : ? string
	{
		return $this->datetime?->format('d-m-Y H:i');
	}

	public function getDateTime() : ? Carbon
	{
		return $this->datetime;
	}

	public function getShippingStatusColorAttribute()
	{
		if($this->shipped_at)
			return '#00ff00';

		foreach($this->contentDeliveries as $contentDelivery)
			if($contentDelivery->isLoaded())
				return '#ffff00';

		return '#ff0000';
	}

	public function getOrders()
	{
		return $this->contentDeliveries->filter(function($contentDelivery)
		{
			return $contentDelivery->getContent()->getOrder();
		})->unique();
	}

	public function getOrdersByClient(string|Client $client) : Collection
	{
		return $this->getOrders()->where('client_id', $client instanceof Client ? $client->getKey() : $client);
	}

	public function hasBeenShipped() : bool
	{
		return !! $this->shipped_at;
	}

	public function hasBeenLoaded() : bool
	{
		return !! $this->loaded_at;
	}

	public function getShipButtonUrl() : string
	{
		return $this->getKeyedRoute('ship');
	}

	public function getUnshipButtonUrl() : string
	{
		return $this->getKeyedRoute('unship');
	}

	public function getShippedAt() : ? Carbon
	{
		return $this->shipped_at;
	}

	public function setShippedAt(? Carbon $shippedAt, bool $save = false) : void
	{
		$this->shipped_at = $shippedAt;

		if($save)
			$this->save();
	}

	public function isShipped() : bool
	{
		return !! $this->getShippedAt();
	}

	private function getHourString($datetime)
	{
		if (($hour = $datetime->hour) < 8)
			return ' - mat1';

		if (($hour = $datetime->hour) < 13)
			return ' - mat2';

		if (($hour = $datetime->hour) < 15)
			return ' - pom1';

		if (($hour = $datetime->hour) < 16)
			return ' - pom2';

		return ' - pom3';
	}

	public function getShortName() : string
	{
		if (! $datetime = $this->datetime)
			return $this->name;

		$pieces = [
			strtoupper($datetime->locale('it')->shortDayName),
			$datetime->day,
			$datetime->locale('it')->shortMonthName,
			$this->getHourString($datetime),
		];

		if (strpos($this->name, 'FIP') !== false)
			$pieces[] = 'FIP';

		return implode(' ', $pieces);

	}

    public function getClients() : ? Collection
    {
		return $this->contentDeliveries->map(function ($item)
		{
			return $item->getClient();
		})->unique();
    }

	public function getSendWarnEmailButton()
	{
		return Button::create([
			'href' => route('deliveries.sendBulkWarnEmail', [$this]),
			'text' => 'deliveries.sendWarnEmail',
			'icon' => 'envelope'
		]);
	}

	public function downloadLoadingList()
	{
		return Button::create([
			'href' => route('deliveries.printFullLoadingList', [$this]),
			// 'target' => '_blank',
			'text' => 'deliveries.printFullLoadingList',
			'icon' => 'pdf'
		]);
	}

	public function getUnshipButton() : Button
	{
		return Button::create([
			'href' => $this->getUnshipButtonUrl(),
			'text' => 'warehouse::delivery.forceUnshipping',
			'icon' => 'download'
		]);
	}

	public function getShipButton() : Button
	{
		return Button::create([
			'href' => $this->getShipButtonUrl(),
			'text' => 'warehouse::delivery.forceShipping',
			'icon' => 'truck-ramp-box'
		]);
	}

	public function getClientDescription()
	{
		return $this->getName();
	}

}