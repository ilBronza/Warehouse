<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldDestinationsList extends DatatableFieldEach
{
	public $width = '20em';

	public $childParameters = [
			'type' => 'clients::destination.city',
			'property' => 'destination',
		];
}