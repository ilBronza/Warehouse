<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;
use IlBronza\Products\Models\Order;

class GroupedContentDeliveryIndexFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'warehouse::fields',
            'fields' => 
            [
				'mySelfPrimary' => 'primary',
	            'firstContentDelivery.mySelfWarn' => [
					'type' => 'function',
		            'function' => 'getWarnClientSelect',
	            ],
	            'mySelfWarnSendCustomEmail.firstContentDelivery' => [
					'type' => 'links.link',
		            'icon' => 'mail',
		            'function' => 'getWarnClientSendCustomEmailUrl',
		            'variable' => 'delivery',
	            ],
	            'mySelfWarnedAtList.contentDeliveries' => 'warehouse::contentDeliveries.contentDeliveriesWarnedAtList',

	            'mySelfPrintRetiringList.firstContentDelivery' => [
					'type' => 'links.link',
		            'function' => 'getPrintLoadingListUrl',
		            'variable' => 'delivery',
		            'faIcon' => 'list-check'
	            ],

	            'client' => 'clients::client.client',

	            'destination_alert' => 'booleanAlarm',


	                           'mySelfOrders.contentDeliveries' => [
	                               'translatedName' => 'Carica/Scarica',
	                               'type' => 'iterators.each',
	                               'childParameters' => [
	                                   'type' => 'function',
	                                   'function' => 'getLoadOrderOnTruckButton'
	                               ],
	                               'width' => '25px'
	                           ],


	            'mySelfFullDestination.destination.name' => 'flat',
	            'mySelfFullDestination.destination.address' => 'addresses::address.fullStreet',
	            'destination' => 'clients::destination.city',
	            'mySelfProvince.destination' => 'clients::destination.province',

	            'mySelfOrders.contentDeliveries' => 'warehouse::contentDeliveries.ordersList',
	            'mySelfProducts.contentDeliveries' => 'warehouse::contentDeliveries.productsList',
	            'mySelfProductDescriptions.contentDeliveries' => 'warehouse::contentDeliveries.productDescriptionsList',

				'mySelfDueDate.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getFormattedDueDateAttribute'
					],
				],

				'mySelfBindello.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'links.link',
						'function' => 'getPrintBindelloUrl',
						'target' => '_blank',
						'faIcon' => 'print'
					],
					'filterable' => false,
					'width' => '25px'
				],

				// 'mySelfWave.contentDeliveries' => [
				// 	'type' => 'iterators.each',
				// 	'childParameters' => [
				// 		'type' => 'property',
				// 		'property' => 'wave'
				// 	],
				// 	'width' => '25px'
				// ],


				'mySelfWave.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getWaveStringAttribute'
					],
					'width' => '25px'
				],

				'mySelfCardboard.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getCardboardStringAttribute'
					],
					'width' => '75px'
				],

				'mySelfSupplier.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getSupplierStringAttribute'
					],
					'width' => '75px'
				],

				'mySelfStock.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getStockStringAttribute'
					],
					'width' => '75px'
				],

	            'mySelfUnitloadsCount.contentDeliveries' => 'warehouse::contentDeliveries.unitloadsCountList',
	            'mySelfDataSheets.contentDeliveries' => 'warehouse::contentDeliveries.dataSheetList',
	            'mySelfPriority.contentDeliveries' => 'warehouse::contentDeliveries.prioritiesList',

	            'mySelfPieces.contentDeliveries' => 'warehouse::contentDeliveries.piecesList',

	            'mySelfDetach.contentDeliveries' => 'warehouse::contentDeliveries.detachList',
	            'mySelfLoad.contentDeliveries' => 'warehouse::contentDeliveries.loadButtonsList',
	            'mySelfUnLoad.contentDeliveries' => 'warehouse::contentDeliveries.unloadButtonsList',




	            ////                'mySelfLoad.orders' => [
	            ////                    'translatedName' => 'Carica/Scarica',
	            ////                    'type' => 'iterators.each',
	            ////                    'childParameters' => [
	            ////                        'type' => 'function',
	            ////                        'function' => 'getLoadOrderOnTruckButton'
	            ////                    ],
	            ////                    'width' => '25px'
	            ////                ],


            ]
        ];
	}
}