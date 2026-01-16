<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryLoaderHelper;
use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryUnloaderHelper;

class ContentDeliveryLoadController extends ContentDeliveriesCRUDController
{
	public $allowedMethods = ['load', 'unload'];

	public function load(string $contentDelivery)
	{
		$contentDelivery = $this->findModel($contentDelivery);

		$success = ContentDeliveryLoaderHelper::gpc()::execute($contentDelivery);

		if(request()->ajax())
			return response()->json([
				'success' => $success,
				'message' => $success ? __('warehouse::messages.contentDeliveryLoadedSuccessfully') : ContentDeliveryLoaderHelper::gpc()::getMessagesBagString($contentDelivery)
			]);

		if($success)
			Ukn::s(__('warehouse::messages.contentDeliveryLoadedSuccessfully'));

		else
			Ukn::e(ContentDeliveryLoaderHelper::gpc()::getMessagesBagString($contentDelivery));

		return back();
	}

	public function unload(string $contentDelivery)
	{
		$contentDelivery = $this->findModel($contentDelivery);

		$success = ContentDeliveryUnloaderHelper::gpc()::execute($contentDelivery);

		if(request()->ajax())
			return response()->json([
				'success' => $success,
				'message' => $success ? __('warehouse::messages.contentDeliveryUnloadedSuccessfully') : ContentDeliveryUnloaderHelper::gpc()::getMessagesBagString($contentDelivery)
			]);

		if($success)
			Ukn::s(__('warehouse::messages.contentDeliveryUnloadedSuccessfully'));

		else
			Ukn::e(ContentDeliveryUnloaderHelper::gpc()::getMessagesBagString($contentDelivery));

		return back();
	}
}
