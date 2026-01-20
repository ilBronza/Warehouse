<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\CRUD\Http\Controllers\BasePackageShowCompleteTrait;
use IlBronza\Warehouse\Models\Delivery\Delivery;

class DeliveryShowController extends DeliveryCRUD
{
    use BasePackageShowCompleteTrait;

    public function getExtendedShowButtons()
    {
    	if (config('warehouse.models.delivery.options.sendGlobalEmailButton'))
	    	$this->addNavbarButton($this->getModel()->getSendWarnEmailButton());

	    $this->addNavbarButton($this->getModel()->downloadLoadingList());

	    if(! $this->getModel()->hasBeenShipped())
	        $this->addNavbarButton(
		        $this->getModel()->getShipButton()
	        );

        else
	        $this->addNavbarButton(
				$this->getModel()->getUnshipButton()
	        );

    }
}
