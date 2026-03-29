<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Buttons\Icons\FaIcon;
use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldFetcher;

class DatatableFieldOrderDeliveryPartial extends DatatableFieldOrderDelivery
{
	// public ? string $translationPrefix = 'warehouse::fields';
	public ? string $forcedStandardName = 'partialDeliveriesList';

	// public $width = '200px';

	public $textParameter = 'deliveries_data_string';

	// public $fetcherData = [
	// 	'urlMethod' => 'getDeliveriesPopup',
	// 	'mode' => 'iframe'
	// ];

	// public function getShippingIcon() : string
	// {
	// 	return FaIcon::inline('truck');
	// }

	// public function transformValue($value)
	// {
	// 	return [
	// 		$this->_getKey($value),
	// 		$value?->{$this->textParameter} ?? $this->getShippingIcon()
	// 	];
	// }

}