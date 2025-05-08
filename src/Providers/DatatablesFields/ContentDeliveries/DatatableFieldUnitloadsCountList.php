<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldUnitloadsCountList extends DatatableFieldEach
{
	public $childParameters = [
			'type' => 'flat',
			'property' => 'unitloads_count',
		];
}