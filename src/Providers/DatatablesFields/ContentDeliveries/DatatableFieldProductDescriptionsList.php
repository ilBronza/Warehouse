<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldProductDescriptionsList extends DatatableFieldEach
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'productDescriptionsList';

	public $childParameters = [
			'type' => 'products::products.shortDescription',
			'property' => 'content.product',
		];
}