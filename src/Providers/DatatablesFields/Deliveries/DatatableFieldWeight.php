<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldFlatColorClass;

class DatatableFieldWeight extends DatatableFieldFlatColorClass
{
	public $decimals = 2;

	public function transformValue($value)
	{
		$maximumWeight = $value->getMaximumWeightKg();
		$suggestedMaximumWeight = $value->getSuggestedMaximumWeightKg();
		$weight = $value->getWeightKg();

		$color = 'green';

		if($maximumWeight && $weight > $maximumWeight)
			$color = 'red';
		else if($suggestedMaximumWeight && $weight > $suggestedMaximumWeight)
			$color = 'orange';

		return [
			number_format($weight, $this->decimals),
			$color
		];
	}
}