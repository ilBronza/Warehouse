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
				'sorting_index' => 'utilities.sorting',

				'mySelfWarnSendCustomEmail.firstContentDelivery' => [
					'type' => 'links.link',
					'faIcon' => 'envelope',
					'function' => 'getWarnClientSendCustomEmailUrl',
					'variable' => 'delivery',
				],
				'warned_at' => [
					'type' => 'editor.toggle',
					'reloadTable' => true,
					'falseIcon' => 'phone'
				],
	            'mySelfWarnedAtList.contentDeliveries' => 'warehouse::contentDeliveries.contentDeliveriesWarnedAtList',

	            'mySelfPrintRetiringList.firstContentDelivery' => [
					'type' => 'links.link',
					'target' => '_blank',
		            'function' => 'getPrintLoadingListUrl',
		            'variable' => 'delivery',
		            'faIcon' => 'rectangle-list',
		            'width' => '45px'
	            ],

	            'client' => 'clients::client.client',

	            'destination_alert' => 'booleanAlarm',


               // 'mySelfOrders.contentDeliveries' => [
               //     'translatedName' => 'Carica/Scarica',
               //     'type' => 'iterators.each',
               //     'childParameters' => [
               //         'type' => 'function',
               //         'function' => 'getLoadOrderOnTruckButton'
               //     ],
               //     'width' => '25px'
               // ],

	            'mySelfFullDestination.destination.name' => [
	            	'type' => 'flat',
	            	'width' => '10em'
	            ],
	            'mySelfFullDestination.destination.address' => [
	            	'type' => 'addresses::address.fullStreet',
	            	'width' => '10em'
	            ],
	            'destination' => [
	            	'type' => 'clients::destination.city',
	            	'width' => '10em'
	            ],
	            'mySelfProvince.destination' => 'clients::destination.province',

	            'mySelfPriority.contentDeliveries' => 'warehouse::contentDeliveries.prioritiesList',
	            'mySelfDataSheets.contentDeliveries' => 'warehouse::contentDeliveries.dataSheetList',

	            'mySelfOrders.contentDeliveries' => 'warehouse::contentDeliveries.ordersList',

	            'mySelfProducts.contentDeliveries' => 'warehouse::contentDeliveries.productsList',

	            'mySelfDetach.contentDeliveries' => 'warehouse::contentDeliveries.detachList',

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
					'width' => '2.4em'
				],

				'mySelfCardboard.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getCardboardStringAttribute'
					],
					'width' => '9em'
				],

				'mySelfSupplier.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getSupplierStringAttribute'
					],
					'width' => '55px'
				],

	            'mySelfPiecesRequired.contentDeliveries' => 'warehouse::contentDeliveries.quantityRequiredList',

				'mySelfStock.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getStockStringAttribute'
					],
					'width' => '45px'
				],

	            'mySelfPiecesDone.contentDeliveries' => [
	            	'type' => 'warehouse::contentDeliveries.quantityDoneList',
	            	'childParameters' => [
						'type' => 'flat',
						'property' => 'quantity_done_string',
					],
					'width' => '55px'
	            ],

				'mySelfProductionStatus.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getProductionStatusList'
					],
					'width' => '175px'
				],

				'mySelfProductModel.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						'function' => 'getStencilName'
					],
					'width' => '8em'
				],

				// 'mySelfDeliveries' => 'warehouse::deliveries.orderDelivery',

				// 'mySelfChangeDeliveries' => 'warehouse::contentDeliveries.changeBulkDelivery',

	            'mySelfUnitloadsCount.contentDeliveries' => 'warehouse::contentDeliveries.unitloadsCountList',
	            'mySelfUnitloadsSizes.contentDeliveries' => 'warehouse::contentDeliveries.unitloadsSizesList',

	            'mySelfGlobalProduce' => 'warehouse::groupedClientsContentDeliveries.globalElaborate',

	            // 'mySelfPieces.contentDeliveries' => 'warehouse::contentDeliveries.piecesList',

	            'mySelfGlobalLoad' => 'warehouse::groupedClientsContentDeliveries.globalLoad',
	            'mySelfLoad.contentDeliveries' => 'warehouse::contentDeliveries.loadButtonsList',
	            'mySelfUnLoad.contentDeliveries' => 'warehouse::contentDeliveries.unloadButtonsList',

	            'mySelfUnLoad.contentDeliveries' => 'warehouse::contentDeliveries.unloadButtonsList',

				'mySelfNotes.contentDeliveries' => [
					'type' => 'iterators.each',
					'childParameters' => [
						'type' => 'function',
						// 'ellipsis' => true,
						'function' => 'getDeliveryNotesCompleteString'
					],
					'width' => '400px'
				],
            ]
        ];
	}
}