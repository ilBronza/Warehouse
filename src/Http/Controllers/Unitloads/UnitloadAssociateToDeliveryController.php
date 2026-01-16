<?php

namespace IlBronza\Warehouse\Http\Controllers\Unitloads;

use IlBronza\FormField\FormField;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryIndexController;

use IlBronza\Warehouse\Models\Unitload\Unitload;

use function request;
use function route;
use function trans;

class UnitloadAssociateToDeliveryController extends DeliveryIndexController
{
	public $avoidCreateButton = true;

	protected string $indexFieldsArraySuffix = 'associateUnitloadToDeliveryIndex';

	public array $scopes = ['current'];

	public function addPostFieldsToTable()
	{
		$unitloads = Unitload::gpc()::whereIn('id', request()->unitloads)->get();

		foreach($unitloads as $unitload)
			$this->getTable()->addPostField(
				FormField::createFromArray([
					'label' => $unitload->getName() . ' (' . $unitload->getQuantity() . ')',
					'type' => 'text',
					'disabled' => true,
					'name' => 'unitloads[]',
					'value' => $unitload->getKey(),
				])
			);

//		$this->getTable()->createPostButton([
//			'href' => app('warehouse')->route('unitloads.associateToDelivery'),
//			'text' => 'warehouse::unitloads.associateToDelivery',
//			'icon' => 'plus'
//		]);
	}
}
