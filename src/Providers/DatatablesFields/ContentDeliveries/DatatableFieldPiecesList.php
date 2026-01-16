<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldPiecesList extends DatatableFieldEach
{
	public $childParameters = [
			'type' => 'numbers.floor',
			'property' => 'quantity',
		];
}