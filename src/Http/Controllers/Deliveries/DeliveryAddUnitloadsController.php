<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Products\Models\Order;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryOrderHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryUnitloadHelper;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Http\Request;

class DeliveryAddUnitloadsController extends DeliveryCRUD
{
    public $allowedMethods = ['addUnitloads'];

	public function addUnitloads(Request $request, string $delivery)
	{
		$request->validate([
			'unitloads' => 'required|array|exists:' . Unitload::gpc()::make()->getTable() . ',id',
		]);

		$delivery = $this->findModel($delivery);

		$unitloads = Unitload::gpc()::whereIn('id', $request->unitloads)->get();

		return DeliveryUnitloadHelper::attachUnitloadsToDelivery($delivery, $unitloads);
	}
}