<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldFunction;
use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldAddGroupedContentDeliveries extends DatatableFieldLink
{
	public $htmlClasses = ['addgroupedcontentdeliveriestodelivery'];
	public $function = 'getAddGroupedContentDeliveriesToDeliveryUrl';
	public ?string $translationPrefix = 'warehouse::datatableFields';

	public $faIcon = 'truck';
}