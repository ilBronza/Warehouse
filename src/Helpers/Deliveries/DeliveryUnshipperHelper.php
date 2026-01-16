<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\CRUD\Traits\Helpers\HelperMessageBagTrait;
use IlBronza\CRUD\Traits\PackagedHelpersTrait;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;

use Illuminate\Database\Eloquent\Model;

use function trans;

class DeliveryUnshipperHelper extends DeliveryBaseShipperHelper
{
	static $classConfigPrefix = 'unshipperHelper';

	public function __execute() : bool
	{
		$delivery = $this->getDelivery();

		$delivery->setShippedAt(null, true);

		return true;
	}

	public function canExecute() : bool
	{
		if(! $this->getDelivery()->isShipped())
		{
			$this->addMessage(trans('warehouse::errors.deliveryNotShipped',
				[
					'delivery' => $this->getDelivery()->getName()
				]));

			return false;

		}

		return true;
	}

}