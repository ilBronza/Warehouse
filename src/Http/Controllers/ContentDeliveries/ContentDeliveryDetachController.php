<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryDetacherHelper;
use Illuminate\Http\Response;

class ContentDeliveryDetachController extends ContentDeliveriesCRUDController
{
	public $allowedMethods = ['detach'];

	public function detach(string $contentDelivery)
	{
		$contentDelivery = $this->findModel($contentDelivery);

		$content = $contentDelivery->getContent();
		$delivery = $contentDelivery->getDelivery();

		DeliveryDetacherHelper::detachContentDelivery($contentDelivery);

		$message = trans('warehouse::contentDelivery.contentDetachedFromDelivery',
			[
				'content' => $content->getName(),
				'delivery' => $delivery->getName()
			]);

		if(request()->ajax())
			return response()->json([
				'success' => true,
				'message' => $message
			]);

		Ukn::s($message);

		return back();
	}
}
