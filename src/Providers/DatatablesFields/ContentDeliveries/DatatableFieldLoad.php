<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Buttons\Icons\FaIcon;
use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldLoad extends DatatableFieldLink
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
				$value->getUnLoadingUrl(),
				FaIcon::inline('download'),
			];

		return [
			$value->getLoadingUrl(),
			FaIcon::inline('upload'),
		];
	}
}