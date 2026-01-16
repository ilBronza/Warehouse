<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldContentDeliveriesWarnedAtList extends DatatableFieldEach
{
	public $childParameters = [
			'type' => 'dates.datetime',
			'property' => 'warned_at',
		];
}