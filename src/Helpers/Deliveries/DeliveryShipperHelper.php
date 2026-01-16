<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use Carbon\Carbon;

class DeliveryShipperHelper extends DeliveryBaseShipperHelper
{
	static $classConfigPrefix = 'shipperHelper';

	public function canExecute() : bool
	{
		if($this->getDelivery()->isShipped())
		{
			$this->addMessage(trans('warehouse::errors.deliveryAlreadyShipped',
				[
					'delivery' => $this->getDelivery()->getName()
				]));

			return false;

		}

		return true;
	}

	public function __execute() : bool
	{
		$delivery = $this->getDelivery();

		$delivery->setShippedAt(Carbon::now(), true);

		return true;
	}
}