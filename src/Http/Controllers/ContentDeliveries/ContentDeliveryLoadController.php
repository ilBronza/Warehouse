<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryDetacherHelper;
use Illuminate\Http\Response;

use function dd;

class ContentDeliveryLoadController extends ContentDeliveriesCRUDController
{
	public $allowedMethods = ['load'];

	public function load(string $contentDelivery)
	{
		$contentDelivery = $this->findModel($contentDelivery);

		dd('crea ContentDeliveryLoaderHelper');
		ContentDeliveryLoaderHelper::load($contentDelivery);

//		$message = trans('warehouse::contentDelivery.contentDetachedFromDelivery',
//			[
//				'content' => $content->getName(),
//				'delivery' => $delivery->getName()
//			]);
//
//		if(request()->ajax())
//			return response()->json([
//				'success' => true,
//				'message' => $message
//			]);
//
//		Ukn::s($message);
//
//		return back();
	}
}
