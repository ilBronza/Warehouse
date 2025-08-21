<?php

namespace IlBronza\Warehouse\Models\Delivery;

use Carbon\Carbon;
use IlBronza\Buttons\Button;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\Vehicles\Models\Vehicle;
use IlBronza\Warehouse\Models\BaseWarehouseModel;

use IlBronza\Warehouse\Models\Unitload\Unitload;

use Illuminate\Support\Collection;

use function __;
use function app;
use function implode;
use function route;

class Delivery extends BaseWarehouseModel
{
	use CRUDUseUuidTrait;

	static $modelConfigPrefix = 'delivery';
	protected $keyType = 'string';

	protected $casts = [
		'datetime' => 'datetime',
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
			'text' => 'deliveries.showDeliveries' . 'CON SUBIOTTI',
			'icon' => 'plus'
		]);

		$button->setAjaxTableButton('order', [
			'openIframe' => true
		]);

		return $button;
	}

	public function getAddOrdersToDeliveryUrl() : string
	{
		return $this->getKeyedRoute('addOrders', [
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

	public function getVolumeMc() : float
	{
		return $this->volume_mc;
	}

	public function getVolumeMcAttribute() : float
	{
		return $this->getContentDeliveries()->sum('volume_mc');
	}

	public function getVolumeMcAvailable() : ? float
	{
		return $this->getVehicle()?->getVolumeMc();
	}

	public function getContentDeliveries() : Collection
	{
		return $this->contentDeliveries;
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

		if(! $loadingVolume = $vehicle->getLoadingVolumeCubicMeters())
			return null;

		if(! $assignedVolume = $this->getAssignedVolumeCubicMeters())
			return 0;

		return $assignedVolume / $loadingVolume * 100;
	}

	public function getWeightKgAttribute() : ? float
	{
		return $this->unitloads->sum('weight_kg');
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
		return $this->unitloads;
	}

	public function vehicle()
	{
		return $this->belongsTo(Vehicle::gpc());
	}

	public function getDateTimeString() : ? string
	{
		return $this->datetime?->format('d-m-Y H:i');
	}

	public function getDateTime() : ? Carbon
	{
		return $this->datetime;
	}

	public function TODO_DOGODO_GRAVE_getShippingStatus()
	{
		if($this->shipped_at === null)
		{
			if($this->loaded_at !== null)
				$color = '#ffff00';
			else
				$color = '#ff0000';
		}
		else
			$color = '#00ff00';

		return __('deliveries.shippingStatus', ['color' => $color]);
	}

	public function getOrdersByClient(string|Client $client) : Collection
	{
		return $this->getOrders()->where('client_id', $client instanceof Client ? $client->getKey() : $client);
	}

}