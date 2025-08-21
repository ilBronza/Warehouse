<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\DatatableFieldFunction;
use IlBronza\Datatables\DatatablesFields\Form\DatatableFieldSubmit;
use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldAddUnitloads extends DatatableFieldSubmit
{
	public $function = 'getAddUnitloadsToDeliveryUrl';
	public ?string $translationPrefix = 'warehouse::datatableFields';
}