<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

class DatatableFieldClientsList extends DatatableFieldEach
{
	public $childParameters = [
			'type' => 'clients::client.client',
			'property' => 'client',
		];
}