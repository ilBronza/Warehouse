<?php

namespace IlBronza\Warehouse;

use IlBronza\CRUD\Providers\RouterProvider\RoutedObjectInterface;
use IlBronza\CRUD\Traits\IlBronzaPackages\IlBronzaPackagesTrait;

use function config;
use function ff;

class Warehouse implements RoutedObjectInterface
{
    use IlBronzaPackagesTrait;

    static $packageConfigPrefix = 'warehouse';

	public function getOrderDeliveryHelperClass() : string
	{
		return config('warehouse.models.delivery.helpers.orderDelivery');
	}

	public function getOrderDeliveryHelper()
	{
		$helperClass = $this->getOrderDeliveryHelperClass();

		return new $helperClass();
	}

    public function manageMenuButtons()
    {
        if(! $menu = app('menu'))
            return;

        $settingsButton = $menu->provideButton([
                'text' => 'generals.settings',
                'name' => 'settings',
                'icon' => 'gear',
                'roles' => ['administrator']
            ]);

        $warehouseManagerButton = $menu->createButton([
            'name' => 'warehouseManager',
            'icon' => 'warehouse',
            'text' => 'warehouse::warehouse.warehouseManager',
        ]);

        $pallettypesButton = $menu->createButton([
            'name' => 'warehousePallettypes',
            'icon' => 'pallet',
            'text' => 'warehouse::warehouse.pallettypes',
            'href' => $this->route('pallettypes.index')
        ]);

        $warehouseManagerButton->addChild($pallettypesButton);


        // $productsButton = $menu->createButton([
        //     'name' => 'products.index',
        //     'icon' => 'users',
        //     'text' => 'products::products.list',
        //     'href' => IbRouter::route($this, 'products.index')
        // ]);

        $settingsButton->addChild($warehouseManagerButton);

        // $warehouseManagerButton->addChild($productsButton);
        // $warehouseManagerButton->addChild(
        //     $menu->createButton([
        //         'name' => 'accessories.index',
        //         'icon' => 'users',
        //         'text' => 'products::accessories.list',
        //         'href' => IbRouter::route($this, 'accessories.index')
        //     ])
        // );
    }
}