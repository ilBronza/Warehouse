<?php

namespace IlBronza\Warehouse\Models\Delivery;

use Carbon\Carbon;
use IlBronza\Buttons\Button;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\Vehicles\Models\Vehicle;
use IlBronza\Warehouse\Models\BaseWarehouseModel;

use IlBronza\Warehouse\Models\Unitload\Unitload;

use function app;
use function route;

class Delivery extends BaseWarehouseModel
{
	use CRUDUseUuidTrait;

	static $modelConfigPrefix = 'delivery';
	protected $keyType = 'string';

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

	public function scopeCurrent($query)
	{
		$query->where('datetime', '>', Carbon::now()->startOfDay()->subDays(2));
	}

	public function contentDeliveries()
	{
		return $this->hasMany(ContentDelivery::gpc());
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

	public function getAssignedVolumeCubicMeters() : ? float
	{
		$result = 0;

		foreach($this->unitloads as $unitload)
			$result += $unitload->getVolumeCubicMeters();

		return $result;
	}
}