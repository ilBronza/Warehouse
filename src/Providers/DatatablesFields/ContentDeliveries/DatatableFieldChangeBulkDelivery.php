<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\DatatableField;

class DatatableFieldChangeBulkDelivery extends DatatableField
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'assignDifferentDelivery';

	public function transformValue($value)
	{
		return $value->client_destination_key;
	}

	// public $childParameters = [
	// 		'type' => 'warehouse::contentDeliveries.content',
	// 		'property' => 'content'
	// 	];
}