<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldDataSheetList extends DatatableFieldEach
{
	public $childParameters = [
			'type' => 'links.pdf',
			'function' => 'getDataSheetUrl',
			'property' => 'content',
		];
}