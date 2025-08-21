<?php

use IlBronza\Warehouse\Helpers\Deliveries\DeliveryOrderHelper;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryUnitloadHelper;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadDeliveryCheckerHelper;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadsDeliveryCheckerHelper;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryDetachController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryIndexController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryLoadController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryLoadCumulativeController;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveryPopupController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddOrdersController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddOrdersIndexController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddUnitloadsController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryByOrderController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryCreateStoreController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryDestroyController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryIndexController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryShowController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeCreateStoreController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeDestroyController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeIndexController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeShowController;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\ContentDeliveryEditUpdateFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\DeliveryCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\PallettypeCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadBulkCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadCrudFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadSplitFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\ContentDeliveryIndexFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryAssociateUnitloadsFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryIndexFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryPickableFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\PallettypeFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadAssociateToDeliveryController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadDestroyController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadPrintController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadSplitController;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use IlBronza\Warehouse\Providers\RelationshipsManagers\DeliveryRelationshipsManager;

return [
    'routePrefix' => 'ibWarehouse',

    'models' => [
	    'delivery' => [
		    'class' => Delivery::class,
		    'table' => 'warehouse__deliveries',
		    'parametersFiles' => [
			    'create' => DeliveryCreateStoreFieldsetsParameters::class,
			    'edit' => DeliveryCreateStoreFieldsetsParameters::class
		    ],
		    'helpers' => [
			    'orderDelivery' => DeliveryOrderHelper::class,
			    'unitloadDelivery' => DeliveryUnitloadHelper::class,
		    ],
		    'relationshipsManagerClasses' => [
			    'show' => DeliveryRelationshipsManager::class
		    ],
		    'controllers' => [
			    'index' => DeliveryIndexController::class,
                'show' => DeliveryShowController::class,
                'edit' => DeliveryEditUpdateController::class,
			    'update' => DeliveryEditUpdateController::class,
			    'create' => DeliveryCreateStoreController::class,
			    'store' => DeliveryCreateStoreController::class,
			    'destroy' => DeliveryDestroyController::class,
			    'addOrders' => DeliveryAddOrdersController::class,
			    'addOrdersIndex' => DeliveryAddOrdersIndexController::class,
			    'orderDeliveriesPopup' => DeliveryByOrderController::class,
			    'addUnitloads' => DeliveryAddUnitloadsController::class
		    ],
		    'fieldsGroupsFiles' => [
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
		    'parametersFiles' => [
			    'edit' => ContentDeliveryEditUpdateFieldsetsParameters::class
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