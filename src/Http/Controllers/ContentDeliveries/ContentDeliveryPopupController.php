<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryDetacherHelper;
use Illuminate\Http\Response;

use function compact;
use function dd;
use function view;

class ContentDeliveryPopupController extends ContentDeliveriesCRUDController
{
	public $allowedMethods = ['popup'];

	public function popup(string $contentDelivery)
	{
		$contentDelivery = $this->findModel($contentDelivery);

		return view('warehouse::contentDeliveries.contentDelivery', compact('contentDelivery'));
	}
}
