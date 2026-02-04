<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use Carbon\Carbon;
use IlBronza\CRUD\CRUD;
use IlBronza\CRUD\Traits\CRUDUpdateEditorTrait;
use IlBronza\Warehouse\Models\Delivery\GroupedContentDelivery;
use Illuminate\Http\Request;

class GroupedContentDeliveryUpdateController extends CRUD
{
	use CRUDUpdateEditorTrait;

	public $allowedMethods = [
		'update'
	];

    public function update(Request $request, $groupedContentDelivery)
    {
    	if($request->field == 'warned_at')
    	{
			$contentDeliveries = GroupedContentDelivery::getContentDeliveriesByKey($groupedContentDelivery);

			if($request->value == true)
			{
				foreach($contentDeliveries as $contentDelivery)
				{
					$contentDelivery->warned = 1;
					$contentDelivery->warned_at = Carbon::now();
					$contentDelivery->save();
				}

				$updateParameters = [];
				$updateParameters['success'] = true;
				$updateParameters['update-editor'] = true;
				$updateParameters['field'] = $request->field;
				$updateParameters['ibaction'] = true;

				return $this->returnUpdateParameters($request, $updateParameters);
			}
			else
			{
				foreach($contentDeliveries as $contentDelivery)
				{
					$contentDelivery->warned = 0;
					$contentDelivery->warned_at = null;
					$contentDelivery->save();
				}

				$updateParameters = [];
				$updateParameters['success'] = true;
				$updateParameters['update-editor'] = true;
				$updateParameters['field'] = $request->field;
				$updateParameters['ibaction'] = true;

				return $this->returnUpdateParameters($request, $updateParameters);
			}
    	}
    }
}
