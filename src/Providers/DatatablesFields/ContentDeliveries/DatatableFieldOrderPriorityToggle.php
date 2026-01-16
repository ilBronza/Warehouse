<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Editor\DatatableFieldToggle;

class DatatableFieldOrderPriorityToggle extends DatatableFieldToggle
{
	public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'priority';

}