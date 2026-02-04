<?php

namespace IlBronza\Warehouse\Http\Controllers\GroupedContentDeliveries;

use IlBronza\Products\Providers\Helpers\OrderProductPhases\OrderProductPhaseCompletionHelper;
use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryLoaderHelper;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveriesCRUDController;
use IlBronza\Warehouse\Models\Delivery\GroupedContentDelivery;

class GroupedContentDeliveryElaborateController extends ContentDeliveriesCRUDController
{
	public $allowedMethods = ['globalElaborate'];

	public function globalElaborate(string $groupedContentDelivery)
	{
		$contentDeliveries = GroupedContentDelivery::getContentDeliveriesByKey($groupedContentDelivery);

		foreach($contentDeliveries as $contentDelivery)
		{
			$orderProduct = $contentDelivery->getContent();

			foreach($orderProduct->getOrderProductPhases() as $orderProductPhase)
			{
				if($orderProductPhase->getWorkstationId() == config('app.sellPhase'))
				{
	                // if($orderProductPhase->isCompleted())
	                //     continue;

	                OrderProductPhaseCompletionHelper::gpc()::execute($orderProductPhase);

	                // $orderProductPhase->forceCompletion('packing');
	                Ukn::s("Completamento di {$orderProductPhase->getName()} completato");
				}
				else
				{
	                Ukn::w("{$orderProductPhase->getName()} non è un prodotto a magazzino");
				}
			}
		}

        return back();
	}
}
