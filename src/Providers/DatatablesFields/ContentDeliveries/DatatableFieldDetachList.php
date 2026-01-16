<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldDetachList extends DatatableFieldEach
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'detachList';

	public $childParameters = [
			'type' => 'warehouse::contentDeliveries.detach',
		];
}