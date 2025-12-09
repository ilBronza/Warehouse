<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

class DatatableFieldZonesList extends DatatableFieldDestinationsList
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'zones';

	public $childParameters = [
			'type' => 'clients::destination.zone',
			'property' => 'destination',
		];
}