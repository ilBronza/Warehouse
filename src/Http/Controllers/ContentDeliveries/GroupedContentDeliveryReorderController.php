<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use IlBronza\Warehouse\Http\Controllers\CRUDWarehousePackageController;
use IlBronza\Warehouse\Models\Delivery\GroupedContentDelivery;
use Illuminate\Http\Request;

class GroupedContentDeliveryReorderController extends CRUDWarehousePackageController
{
	public $configModelClassName = 'groupedContentDelivery';
    public $allowedMethods = ['storeMassReorder'];

	public function storeMassReorder(Request $request)
	{
		$request->validate([
			'indexes' => 'array|required',
		]);		

		$sortingIndexes = $request->indexes;

		foreach($sortingIndexes as $id => $sortingIndex)
		{
			$contentDeliveries = GroupedContentDelivery::getContentDeliveriesByKey($id);

			foreach($contentDeliveries as $contentDelivery)
			{
				$contentDelivery->setSortingIndex($sortingIndex);
				$contentDelivery->save();
			}
		}

		return ['success' => true];
	}

}
