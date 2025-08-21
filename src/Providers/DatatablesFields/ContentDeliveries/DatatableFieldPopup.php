<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Buttons\Icons\FaIcon;
use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldFetcher;

class DatatableFieldPopup extends DatatableFieldFetcher
{
	public $width = '35px';

	public function hasText()
	{
		return true;
	}

	public $fetcherData = [
		'urlMethod' => 'getPopupUrl',
	];

	public function transformValue($value)
	{
		return [
			$this->_getKey($value),
			FaIcon::inline('truck')
		];
	}

}