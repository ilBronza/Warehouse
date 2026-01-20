<?php

use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryLoaderHelper;
use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryUnloaderHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryAutomaticCreatorHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryOrderHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryOrderProductHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryShipperHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryUnitloadHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryUnshipperHelper;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadDeliveryCheckerHelper;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadLoaderHelper;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadMeasuresCalculatorHelper;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadUnloaderHelper;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadsDeliveryCheckerHelper;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryDetachController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryIndexController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryLoadController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryLoadCumulativeController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryPopupController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\GroupedContentDeliveryIndexController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryActiveController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddGroupedContentDeliveriesController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddGroupedContentDeliveriesIndexController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddOrdersController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddOrdersIndexController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddUnitloadsController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAutomaticCreationController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryByOrderController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryCreateStoreController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryDestroyController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryIndexController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryShipController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryShowController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeCreateStoreController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeDestroyController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeIndexController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeShowController;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\ContentDeliveryEditUpdateFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\DeliveryCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\DeliveryShowFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\PallettypeCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadBulkCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadCrudFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadSplitFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\ContentDeliveryIndexFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryActiveFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryAssociateUnitloadsFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryIndexFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryPickableFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\GroupedContentDeliveryIndexFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\PallettypeFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadAssociateToDeliveryController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadDestroyController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadPrintController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadSplitController;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Delivery\GroupedContentDelivery;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use IlBronza\Warehouse\Providers\RelationshipsManagers\DeliveryRelationshipsManager;

return [
    'routePrefix' => 'ibWarehouse',


    'datatableFieldWidths' => [
    	'deliveries' => [
			'datatableFieldUnitloadsCount' => '3em',
    		'datatableFieldDelivery' => '12em',
    		'datatableFieldName' => '20em',
		    'datatableFieldOrderDelivery' => '10em',
    		'datatableFieldOrderProductPhaseDelivery' => '6em',
    		'datatableFieldAddUnitloads' => '2em',
    		'datatableFieldAddOrders' => '2em',
		    'datatableFieldOrderHasAllDeliveringUnitloads' => '2em',
		    'datatableFieldWeight' => '5em',
		    'datatableFieldVolume' => '3em'
    	],
		'contentDeliveries' => [
			'datatableFieldChangeBulkDelivery' => '22em',
			'datatableFieldDetach' => '2em',
			'datatableFieldDetachList' => '2em',
			'datatableFieldLoadCumulative' => '2em',
			'datatableFieldLoadButtonsList' => '2em',
			'datatableFieldLoad' => '2em',
			'datatableFieldUnload' => '2em',
			'datatableFieldPopup' => '12em',
			'datatableFieldContentList' => '12em',
			'datatableFieldContent' => '10em',
			'datatableFieldClientsList' => '20em',
			'datatableFieldDestinationsList' => '12em',
			'datatableFieldDataSheetList' => '2em',
			'datatableFieldPrioritiesList' => '2em',
			'datatableFieldZonesList' => '2em',
			'datatableFieldUnitloadsCountList' => '2em',
			'datatableFieldUnloadButtonsList' => '2em',
			'datatableFieldContentDeliveriesWarnedAtList' => '10em',
			'datatableFieldProductsList' => '4em',
			'datatableFieldOrdersList' => '4em',
			'datatableFieldProductDescriptionsList' => '16em',
			'datatableFieldPiecesList' => '3em'
		],
		'groupedClientsContentDeliveries' => [
			'datatableFieldEntiresList' => '5em',
			'datatableFieldAddGroupedContentDeliveries' => '2em',
			'datatableFieldContentList' => '2em',
			'datatableFieldClientsList' => '20em',
			'datatableFieldDestinationsList' => '12em',
			'datatableFieldZonesList' => '2em',
			'datatableFieldZone' => '2em',
			'datatableFieldUnitloadsCountList' => '2em',
			'datatableFieldVolumesList' => '4em',
			'datatableFieldWeightsList' => '3.3em',
			'datatableFieldPartialsList' => '2em'
		]
    ],

	'models' => [
	    'delivery' => [
		    'class' => Delivery::class,
		    'table' => 'warehouse__deliveries',
		    'parametersFiles' => [
			    'create' => DeliveryCreateStoreFieldsetsParameters::class,
			    'show' => DeliveryShowFieldsetsParameters::class,
			    'edit' => DeliveryCreateStoreFieldsetsParameters::class
		    ],
			'helpers' => [
				'automaticCreator' => DeliveryAutomaticCreatorHelper::class,
				'orderDelivery' => DeliveryOrderHelper::class,

				'orderProductDelivery' => DeliveryOrderProductHelper::class,

				'unitloadDelivery' => DeliveryUnitloadHelper::class,
				'shipperHelper' => DeliveryShipperHelper::class,
				'unshipperHelper' => DeliveryUnshipperHelper::class,
			],
		    'relationshipsManagerClasses' => [
			    'show' => DeliveryRelationshipsManager::class
		    ],
		    'controllers' => [
			    'automaticCreation' => DeliveryAutomaticCreationController::class,
			    'active' => DeliveryActiveController::class,
				'index' => DeliveryIndexController::class,
				'show' => DeliveryShowController::class,
				'ship' => DeliveryShipController::class,
				'unship' => DeliveryShipController::class,
				'edit' => DeliveryEditUpdateController::class,
				'update' => DeliveryEditUpdateController::class,
				'create' => DeliveryCreateStoreController::class,
				'store' => DeliveryCreateStoreController::class,
				'destroy' => DeliveryDestroyController::class,
				'addOrders' => DeliveryAddOrdersController::class,
				'addOrdersIndex' => DeliveryAddOrdersIndexController::class,
				'orderDeliveriesPopup' => DeliveryByOrderController::class,
				'addUnitloads' => DeliveryAddUnitloadsController::class,


				'addGroupedContentDeliveries' => DeliveryAddGroupedContentDeliveriesController::class,
				'addGruopedContentDeliveriesIndex' => DeliveryAddGroupedContentDeliveriesIndexController::class,

		    ],
		    'fieldsGroupsFiles' => [
				'active' => DeliveryActiveFieldsGroupParametersFile::class,
				'index' => DeliveryIndexFieldsGroupParametersFile::class,
				'associateUnitloadToDeliveryIndex' => DeliveryAssociateUnitloadsFieldsGroupParametersFile::class,
				'pickable' => DeliveryPickableFieldsGroupParametersFile::class
		    ],
	    ],
	    'contentDelivery' => [
		    'class' => ContentDelivery::class,
		    'table' => 'warehouse__content_deliveries',
		    'controllers' => [
			    'detach' => ContentDeliveryDetachController::class,
			    'loadCumulative' => ContentDeliveryLoadCumulativeController::class,
			    'unloadCumulative' => ContentDeliveryLoadCumulativeController::class,
			    'load' => ContentDeliveryLoadController::class,
			    'unload' => ContentDeliveryLoadController::class,
			    'popup' => ContentDeliveryPopupController::class,
			    'edit' => ContentDeliveryEditUpdateController::class,
				'index' => ContentDeliveryIndexController::class,
		    ],
		    'fieldsGroupsFiles' => [
			    'index' => ContentDeliveryIndexFieldsGroupParametersFile::class,
			    'related' => ContentDeliveryIndexFieldsGroupParametersFile::class,
		    ],
			'helpers' => [
				'loaderHelper' => ContentDeliveryLoaderHelper::class,
				'unloaderHelper' => ContentDeliveryUnloaderHelper::class,
			],
		    'parametersFiles' => [
			    'edit' => ContentDeliveryEditUpdateFieldsetsParameters::class
		    ],
	    ],
	    'groupedContentDelivery' => [
	    	'class' => GroupedContentDelivery::class,
		    'fieldsGroupsFiles' => [
			    'related' => GroupedContentDeliveryIndexFieldsGroupParametersFile::class,
		    ],
		    'controllers' => [
				'index' => GroupedContentDeliveryIndexController::class,
		    ],
	    ],
        'unitload' => [
            'class' => Unitload::class,
            'table' => 'warehouse__unitloads',
	        'baseUnitloadVolumeCubicMeters' => 1.4,
            'parametersFiles' => [
                'bulkCreate' => UnitloadBulkCreateStoreFieldsetsParameters::class,
	            'crud' => UnitloadCrudFieldsetsParameters::class,
                'split' => UnitloadSplitFieldsetsParameters::class
            ],
	        'helpers' => [
		        'unitloadDeliveryCheckerHelper' => UnitloadDeliveryCheckerHelper::class,
		        'unitloadsDeliveryCheckerHelper' => UnitloadsDeliveryCheckerHelper::class,
		        'measuresHelper' => UnitloadMeasuresCalculatorHelper::class,
		        'loaderHelper' => UnitloadLoaderHelper::class,
		        'unloaderHelper' => UnitloadUnloaderHelper::class,
	        ],
            'controllers' => [
	            'associateToDelivery' => UnitloadAssociateToDeliveryController::class,
	            'index' => UnitloadPrintController::class,
	            'print' => UnitloadPrintController::class,
	            'split' => UnitloadSplitController::class,
                'resetPrintedAt' => UnitloadPrintController::class,
                'edit' => UnitloadEditUpdateController::class,
                'update' => UnitloadEditUpdateController::class,
                'destroy' => UnitloadDestroyController::class,
            ]
        ],
        'pallet' => [
            'class' => Pallet::class,
            'table' => 'warehouse__pallets'
        ],
        'pallettype' => [
            'class' => Pallettype::class,
            'table' => 'warehouse__pallettypes',
            'fieldsGroupsFiles' => [
                'index' => PallettypeFieldsGroupParametersFile::class
            ],
            'parametersFiles' => [
                'crud' => PallettypeCreateStoreFieldsetsParameters::class
            ],
            'controllers' => [
                'index' => PallettypeIndexController::class,
                'create' => PallettypeCreateStoreController::class,
                'store' => PallettypeCreateStoreController::class,
                'show' => PallettypeShowController::class,
                'edit' => PallettypeEditUpdateController::class,
                'update' => PallettypeEditUpdateController::class,
                'destroy' => PallettypeDestroyController::class,
            ]
        ]
    ]
];