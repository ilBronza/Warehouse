<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldGlobalElaborate extends DatatableFieldLink
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'globalElaborate';

	public $faIcon = 'truck-ramp-box';

	public $function = 'getGlobalElaborateUrl';
}