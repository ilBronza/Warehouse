<?php

use IlBronza\Warehouse\Helpers\DeliveryOrderHelper;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddOrdersController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddOrdersIndexController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryCreateStoreController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryIndexController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeCreateStoreController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeDestroyController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeIndexController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeShowController;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\DeliveryCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\PallettypeCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadBulkCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadCrudFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryIndexFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\DeliveryPickableFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\PallettypeFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadDestroyController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadPrintController;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use IlBronza\Warehouse\Models\Unitload\Unitload;

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
		    ],
		    'controllers' => [
			    'index' => DeliveryIndexController::class,
			    'edit' => DeliveryEditUpdateController::class,
			    'update' => DeliveryEditUpdateController::class,
			    'create' => DeliveryCreateStoreController::class,
			    'store' => DeliveryCreateStoreController::class,
			    'addOrders' => DeliveryAddOrdersController::class,
			    'addOrdersIndex' => DeliveryAddOrdersIndexController::class,
		    ],
		    'fieldsGroupsFiles' => [
			    'index' => DeliveryIndexFieldsGroupParametersFile::class,
			    'pickable' => DeliveryPickableFieldsGroupParametersFile::class
		    ],
	    ],
	    'contentDelivery' => [
		    'class' => ContentDelivery::class,
		    'table' => 'warehouse__content_deliveries',
	    ],
        'unitload' => [
            'class' => Unitload::class,
            'table' => 'warehouse__unitloads',
	        'baseUnitloadVolumeCubicMeters' => 1.4,
            'parametersFiles' => [
                'bulkCreate' => UnitloadBulkCreateStoreFieldsetsParameters::class,
                'crud' => UnitloadCrudFieldsetsParameters::class
            ],
            'controllers' => [
                'index' => UnitloadPrintController::class,
                'print' => UnitloadPrintController::class,
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