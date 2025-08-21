<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;
use IlBronza\Products\Models\Order;

class ContentDeliveryIndexFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'warehouse::fields',
            'fields' => 
            [
				'mySelfPrimary' => 'primary',
	            'mySelfEdit' => 'links.edit',
	            'delivery' => 'warehouse::deliveries.delivery',
	            'client' => 'clients::client.client',
	            'mySelfWarn' => [
		            'type' => 'function',
		            'function' => 'getWarnClientSelect',
		            'width' => '65px',
	            ],

	            'mySelfWarnSendCustomEmail' => [
		            'type' => 'links.link',
		            'icon' => 'mail',
		            'function' => 'getWarnClientSendCustomEmailUrl',
		            'roles' => 'organizator',
		            'width' => '20px',
	            ],
	            'order.warned_at' => [
			            'type' => 'dates.datetime',
			            'property' => 'warned_at'
	            ],

	            'mySelfPrintRetiringList' => [
		            'type' => 'links.link',
		            'lightbox' => true,
		            'function' => 'getPrintLoadingListUrl',
		            'variable' => 'delivery',
		            'faIcon' => 'list-check'
	            ],

	            'destination' => 'clients::destination.short',
	            'mySelfProvince.destination' => 'clients::destination.province',

	            'order' => 'products::orders.order',

	            'content' => [
					'type' => 'links.pdf',
		            'lightbox' => true,
		            'function' => 'getDataSheetUrl',
	            ],

	            'content.product' => 'products::products.product',
	            'mySelfShort.content.product' => 'products::products.shortDescription',
	            'mySelfType.content.product.description' => 'flat',
	            'mySelfSizes.content.product' => [
	            	'type' => 'function',
	            	'function' => 'getProductSizes'
	            ],

	            'mySelfPriority.content.order' => [
		            'type' => 'editor.toggle',
		            'solveElement' => true,
		            'placeholderElement' => Order::gpc()::make(),
		            'editorProperty' => 'priority'
	            ],

	            'mySelfDetachFromDelivery' => 'warehouse::contentDeliveries.detach',
	            'mySelfloadCumulativeProducts' => 'warehouse::contentDeliveries.loadCumulative',

	            'mySelfloadProducts' => 'warehouse::contentDeliveries.load',

	            'mySelfDueDate.content.due_date' => 'dates.date',
	            'mySelfPopup' => 'warehouse::contentDeliveries.popup',

	            'mySelfPhases.content' => [
					'type' => 'function',
		            'function' => 'getOrderProductPhasesLoadingList',
		            'width' => '155px'
	            ],

	            'mySelfPhasesWaves.content.product.wave' => [
					'type' => 'flat',
		            'width' => '35px'
	            ],

	            'mySelfPhasesSupplier.content' => [
					'type' => 'function',
		            'function' => 'getOrderProductSuppliersList',
		            'width' => '45px'
	            ],

	            'quantity' => 'flat',
	            'quantity_required' => 'flat',

	            'mySelfPhasesStocks.content' => [
					'type' => 'function',
		            'function' => 'getOrderProductStocksList',
		            'width' => '45px'
	            ],

	            'mySelfNotes.content.order' => [
					'type' => 'function',
		            'function' => 'getDeliveryNotesCompleteString',
		            'width' => '400px'
	            ],

            ]
        ];
	}
}