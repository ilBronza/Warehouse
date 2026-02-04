<?php

namespace IlBronza\Warehouse\Models\Delivery;

use Carbon\Carbon;
use IlBronza\Buttons\Button;
use IlBronza\CRUD\Traits\Model\CRUDModelTrait;
use IlBronza\CRUD\Traits\Model\CRUDReorderableStandardTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\Clients\Models\Client;
use IlBronza\Clients\Models\Destination;
use IlBronza\Products\Models\Order;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryDetacherHelper;
use IlBronza\Warehouse\Models\BaseWarehouseModel;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Interfaces\DeliverableInterface;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Collection;
use function array_merge;
use function is_string;
use function route;

class GroupedContentDelivery extends BaseWarehouseModel
{
	use CRUDReorderableStandardTrait;
	static $modelConfigPrefix = 'groupedContentDelivery';

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

	public function getGlobalLoadUrl() : string
	{
		return $this->getKeyedRoute('globalLoad');
	}

	public function getGlobalElaborateUrl() : string
	{
		return $this->getKeyedRoute('globalElaborate');
	}

	static function getDeliveryFromKey(string $key) : Delivery
	{
		$pieces = explode("_", $key);

		$deliveryId = $pieces[0];

		return Delivery::gpc()::find($deliveryId);
	}

	static function getClientFromKey(string $key) : Client
	{
		$pieces = explode("_", $key);

		$clientId = $pieces[1];

		return Client::gpc()::find($clientId);
	}

	static function getDestinationFromKey(string $key) : Destination
	{
		$pieces = explode("_", $key);

		$destinationId = $pieces[2];

		return Destination::gpc()::find($destinationId);
	}

	static function getContentDeliveriesByKey(string $key) : Collection
	{
		$pieces = explode("_", $key);

		return ContentDelivery::where('delivery_id', $pieces[0])->with('content.order')->get()->filter(function($item) use($pieces)
			{
				if($item->getContent()?->getOrder()?->client_id != $pieces[1])
					return false;

				return $item->getContent()?->getOrder()?->destination_id == $pieces[2];
			});
	}

	public function getCalculatedSortingIndex() : ? int
	{
		return $this->contentDeliveries?->first()?->sorting_index ?? 0;
	}

	public function getAddGroupedContentDeliveriesToDeliveryButton() : Button
	{
		$button = Button::create([
			'href' => static::getAddGroupedContentDeliveriesToDeliveryIndexUrl(),
			'text' => 'warehouse::deliveries.associateDelivery',
			'icon' => 'plus'
		]);

		$button->setAjaxTableButton('order', [
			'openIframe' => true
		]);

		$button->setPrimary();

		return $button;
	}

	static public function getAddGroupedContentDeliveriesToDeliveryIndexUrl() : string
	{
		return app('warehouse')->route('deliveries.addGroupedContentDeliveriesIndex');
	}

	public function getWarnedAtAttribute()
	{
		$contentDeliveries = static::getContentDeliveriesByKey($this->getKey());

		foreach($contentDeliveries as $contentDelivery)
			if(! $contentDelivery->hasBeenWarned())
				return false;

		if(count($contentDeliveries) > 0)
			return true;

		return false;
	}


}