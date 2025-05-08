<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldContentList extends DatatableFieldEach
{
	public $childParameters = [
			'type' => 'warehouse::contentDeliveries.content',
			'property' => 'content'
		];
}