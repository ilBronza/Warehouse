<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Buttons\Icons\FaIcon;
use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldLoadCumulative extends DatatableFieldLink
{
	public ?string $translationPrefix = 'warehouse::datatableFields';

	public function hasText()
	{
		return true;
	}

	public function transformValue($value)
	{
		if($value->isLoaded())
			return [
				$value->getCumulativeUnLoadingUrl(),
				FaIcon::inline('angles-down'),
			];

		return [
			$value->getCumulativeLoadingUrl(),
			FaIcon::inline('angles-up'),
		];
	}
}