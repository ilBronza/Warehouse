<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldGlobalLoad extends DatatableFieldLink
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'globalLoad';

	public $faIcon = 'truck-ramp-box';

	public $function = 'getGlobalLoadUrl';
}