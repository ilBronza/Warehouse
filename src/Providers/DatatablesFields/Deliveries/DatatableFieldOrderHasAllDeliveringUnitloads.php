<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldBoolean;

use function is_null;

class DatatableFieldOrderHasAllDeliveringUnitloads extends DatatableFieldBoolean
{
	public function transformValue($value)
	{
		foreach($value->getDeliveringChildren() as $child)
		{
			if(count($contentDeliveries = $child->getContentDeliveries()) == 0)
				return false;

			foreach($contentDeliveries as $contentDelivery)
				if($contentDelivery->isPartial())
					return false;
		}

		return true;
	}
}