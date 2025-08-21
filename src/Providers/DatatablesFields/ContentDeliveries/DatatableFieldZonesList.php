<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

class DatatableFieldZonesList extends DatatableFieldDestinationsList
{
	public $childParameters = [
			'type' => 'clients::destination.zone',
			'property' => 'destination',
		];
}