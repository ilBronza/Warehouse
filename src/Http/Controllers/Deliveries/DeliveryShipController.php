<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Ukn\Ukn;

use IlBronza\Warehouse\Helpers\Deliveries\DeliveryShipperHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryUnshipperHelper;

class DeliveryShipController extends DeliveryCRUD
{
    public $allowedMethods = ['ship', 'unship'];

    public function ship($delivery)
    {
        $delivery = $this->findModel($delivery);

	    $success = DeliveryShipperHelper::gpc()::execute($delivery);

	    if($success)
		    Ukn::s(__('warehouse::messages.deliveryShippedSuccessfully', ['delivery' => $delivery->getName()]));

	    else
		    Ukn::e(DeliveryShipperHelper::gpc()::getMessagesBagString($delivery));

	    return back();
    }

	public function unship($delivery)
	{
		$delivery = $this->findModel($delivery);

	    $success = DeliveryUnshipperHelper::gpc()::execute($delivery);

	    if($success)
		    Ukn::s(__('warehouse::messages.deliveryUnshippedSuccessfully', ['delivery' => $delivery->getName()]));

	    else
		    Ukn::e(DeliveryUnshipperHelper::gpc()::getMessagesBagString($delivery));

	    return back();
	}
}
