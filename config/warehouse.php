<?php

use App\Models\Pallet;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeCreateStoreController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeDestroyController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeIndexController;
use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeShowController;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\PallettypeCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadBulkCreateStoreFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadCrudFieldsetsParameters;
use IlBronza\Warehouse\Http\Controllers\Parameters\Tables\PallettypeFieldsGroupParametersFile;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadDestroyController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadEditUpdateController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadPrintController;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use IlBronza\Warehouse\Models\Unitload\Unitload;

return [
    'routePrefix' => 'ibWarehouse',

    'models' => [
        'unitload' => [
            'class' => Unitload::class,
            'table' => 'warehouse__unitloads',
            'parametersFiles' => [
                'bulkCreate' => UnitloadBulkCreateStoreFieldsetsParameters::class,
                'crud' => UnitloadCrudFieldsetsParameters::class
            ],
            'controllers' => [
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