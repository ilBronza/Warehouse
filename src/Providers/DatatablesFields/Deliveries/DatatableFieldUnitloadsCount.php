<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldFlat;

class DatatableFieldUnitloadsCount extends DatatableFieldFlat
{
	public function transformValue($value)
	{
		return $value->contentDeliveries->sum('unitloads_count');
	}
}