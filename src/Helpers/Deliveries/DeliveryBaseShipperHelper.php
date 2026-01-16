<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\CRUD\Traits\Helpers\HelperMessageBagTrait;
use IlBronza\CRUD\Traits\PackagedHelpersTrait;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;

use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Database\Eloquent\Model;

use function trans;

class DeliveryBaseShipperHelper
{
	use PackagedHelpersTrait;
	use HelperMessageBagTrait;

	public Delivery $delivery;

	static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'delivery';

	public function __construct(Delivery $delivery)
	{
		$this->delivery = $delivery;
	}

	static function execute(Delivery $delivery) : bool
	{
		$result = new static($delivery);

		return $result->_execute();
	}

	public function getSubjectModel() : Model
	{
		return $this->getDelivery();
	}

	public function getDelivery() : Delivery
	{
		return $this->delivery;
	}

	public function _execute() : bool
	{
		if(! $this->canExecute())
		{
			$this->addMessage(trans('warehouse::errors.' . static::$cantExecuteError,
				[
					'contentDelivery' => $this->getContentDelivery()->getName(),
				]));

			return false;
		}

		return $this->__execute();
	}


}