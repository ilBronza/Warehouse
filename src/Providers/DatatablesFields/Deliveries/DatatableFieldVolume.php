<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldFlatColorClass;

class DatatableFieldVolume extends DatatableFieldFlatColorClass
{
	public function transformValue($value)
	{
		$maximumVolume = $value->getMaximumVolumeMc();
		$suggestedMaximumVolume = $value->getSuggestedMaximumVolumeMc();
		$volume = $value->getVolumeMc();

		$color = 'green';

		if($maximumVolume && $volume > $maximumVolume)
			$color = 'red';
		else if($suggestedMaximumVolume && $volume > $suggestedMaximumVolume)
			$color = 'orange';

		return [
			number_format($volume, 2),
			$color
		];
	}
}