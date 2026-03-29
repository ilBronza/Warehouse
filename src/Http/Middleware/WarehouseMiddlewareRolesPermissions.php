<?php

namespace IlBronza\Warehouse\Http\Middleware;

use IlBronza\CRUD\Middleware\CRUDBasePackageMiddlewareRolesPermissions;

/**
 * Resolves allowed roles for Warehouse routes from config (warehouse.defaultRoles / warehouse.routeRoles).
 */
class WarehouseMiddlewareRolesPermissions extends CRUDBasePackageMiddlewareRolesPermissions
{
    protected string $configPackageName = 'warehouse';
}
