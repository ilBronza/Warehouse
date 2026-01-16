<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldProductsList extends DatatableFieldEach
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'productsList';

	public $childParameters = [
			'type' => 'products::products.product',
			'property' => 'content.product',
		];
}