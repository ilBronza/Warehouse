<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

class DatatableFieldClientsList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'clients';

	public function _transformValue($value)
	{
		$string = $value->first()?->getContent()->getClient()->getName();

		return "<span class=\"uk-text-truncate\">{$string}</span>";
	}
}