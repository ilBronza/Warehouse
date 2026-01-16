<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldBoolean;

use function is_null;

class DatatableFieldOrderHasAllDeliveringUnitloads extends DatatableFieldBoolean
{
	public function transformValue($value)
	{
		foreach($value->getDeliveringChildren() as $child)
			if(($child->unitloads->count() == 0)||($child->unitloads_without_delivery_count))
				return false;

		return true;
	}
}