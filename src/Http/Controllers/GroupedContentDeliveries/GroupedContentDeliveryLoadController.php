<?php

namespace IlBronza\Warehouse\Http\Controllers\GroupedContentDeliveries;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryLoaderHelper;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveriesCRUDController;
use IlBronza\Warehouse\Models\Delivery\GroupedContentDelivery;

class GroupedContentDeliveryLoadController extends ContentDeliveriesCRUDController
{
	public $allowedMethods = ['globalLoad'];

	public function globalLoad(string $groupedContentDelivery)
	{
		$contentDeliveries = GroupedContentDelivery::getContentDeliveriesByKey($groupedContentDelivery);

		$success = true;
		$messages = [];

		foreach($contentDeliveries as $contentDelivery)
			if(! ContentDeliveryLoaderHelper::gpc()::execute($contentDelivery))
			{
				$messages[] = ContentDeliveryLoaderHelper::gpc()::getMessagesBagString($contentDelivery);
				$success = false;
			}

		$messagesString = implode("<br />", $messages);

		if(request()->ajax())
			return response()->json([
				'success' => $success,
				'message' => $success ? __('warehouse::messages.contentDeliveryLoadedSuccessfully') : $messagesString
			]);

		if($success)
			Ukn::s(__('warehouse::messages.contentDeliveryLoadedSuccessfully'));

		else
			Ukn::e($messagesString);

		return back();
	}
}
