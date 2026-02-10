<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldFlatColorClass;

class DatatableFieldOrderProductPhaseDelivery extends DatatableFieldFlatColorClass
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'deliveriesTime';

	public function transformValue($value)
	{
		if(! count($deliveries = $value->getContentForDelivery()->getDeliveries()))
			return [null, null];

		$result = [];

		foreach($deliveries as $delivery)
			// $result[] = $delivery->delivery_datetime?->format('d/m H:i');
			$result[] = $delivery->getShortNameForProduction();

		$closer = $deliveries->sortBy('delivery_datetime')->first();

		$dayClass = $closer->delivery_datetime->locale('it')->shortDayName;

		return [
			implode('<br />', $result),
			$dayClass
		];
	}
}