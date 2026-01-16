<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldLoadButtonsList extends DatatableFieldEach
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'loadList';

	public $childParameters = [
			'type' => 'warehouse::contentDeliveries.load',
		];
}