<?php

namespace IlBronza\Warehouse\Http\Controllers\Unitloads;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryDetacherHelper;

class UnitloadDetachFromDeliveryController extends UnitloadsCRUDController
{
	public $allowedMethods = ['detach'];

	public function detach(string $unitload)
	{
		$unitload = $this->findModel($unitload);

		$contentDelivery = $unitload->getContentDelivery();

		if (! $contentDelivery) {
			Ukn::e(trans('warehouse::unitloads.unitloadNotAssociatedToDelivery'));

			return back();
		}

		DeliveryDetacherHelper::detachUnitload($unitload, $contentDelivery, false);

		$message = trans('warehouse::unitloads.unitloadDetachedFromDelivery', [
			'unitload' => $unitload->getName(),
		]);

		if (request()->ajax()) {
			return response()->json([
				'success' => true,
				'message' => $message,
			]);
		}

		Ukn::s($message);

		return back();
	}
}
