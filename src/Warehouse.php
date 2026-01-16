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

	public function getUnitloadDeliveryHelperClass() : string
	{
		return config('warehouse.models.delivery.helpers.unitloadDelivery');
	}

	public function getOrderDeliveryHelper()
	{
		$helperClass = $this->getOrderDeliveryHelperClass();

		return new $helperClass();
	}

	public function getUnitloadDeliveryHelper()
	{
		$helperClass = $this->getUnitloadDeliveryHelperClass();

		return new $helperClass();
	}

    public function manageMenuButtons()
    {
        if(! $menu = app('menu'))
            return;

        $settingsButton = $menu->provideButton([
                'text' => 'menu::menu.settings',
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

        $deliveriesButton = $menu->createButton([
            'name' => 'warehousedeliveries',
            'icon' => 'truck-ramp-box',
            'text' => 'warehouse::deliveries.deliveries',
            'children' => [
                [
                    'name' => 'warehouseActiveDeliveries',
                    'icon' => 'list',
                    'text' => 'warehouse::deliveries.active',
                    'href' => $this->route('deliveries.active'),
                ],
	            [
		            'name' => 'warehouseDeliveries',
		            'icon' => 'database',
		            'text' => 'warehouse::deliveries.archive',
		            'href' => $this->route('deliveries.index'),
	            ],
	            [
	            'name' => 'warehouseDeliveriesAutomaticCreationForm',
	            'icon' => 'calendar-plus',
	            'text' => 'warehouse::deliveries.automaticCreation',
	            'href' => $this->route('deliveries.automaticCreationForm'),
            ]
            ]
        ]);

        $warehouseManagerButton->addChild($pallettypesButton);
        $warehouseManagerButton->addChild($deliveriesButton);


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