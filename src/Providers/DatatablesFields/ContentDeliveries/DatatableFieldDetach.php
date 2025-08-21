<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldDetach extends DatatableFieldLink
{
	public $function = 'getDetachUrl';
	public ?string $translationPrefix = 'warehouse::datatableFields';

	public $faIcon = 'minus';

}