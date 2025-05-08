<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldFunction;
use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldAddOrders extends DatatableFieldLink
{
	public $htmlClasses = ['addorderstodelivery'];
	public $function = 'getAddOrdersToDeliveryUrl';
	public ?string $translationPrefix = 'warehouse::datatableFields';

	public $faIcon = 'truck';

}