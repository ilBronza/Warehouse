<?php

namespace IlBronza\Warehouse;

use Carbon\Carbon;
use IlBronza\CRUD\Providers\RouterProvider\RoutedObjectInterface;
use IlBronza\CRUD\Traits\IlBronzaPackages\IlBronzaPackagesTrait;

use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Support\Str;

use function config;
use function ff;
use function trans;

class Warehouse implements RoutedObjectInterface
{
    use IlBronzaPackagesTrait;

    static $packageConfigPrefix = 'warehouse';

	public function getOrderDeliveryHelperClass() : string
	{
		return config('warehouse.models.delivery.helpers.orderDelivery');
	}

    public function getOrderProductDeliveryHelperClass() : string
    {
        return config('warehouse.models.delivery.helpers.orderProductDelivery');        
    }

	public function getUnitloadDeliveryHelperClass() : string
	{
		return config('warehouse.models.delivery.helpers.unitloadDelivery');
	}

    public function getOrderProductDeliveryHelper()
    {
        $helperClass = $this->getOrderProductDeliveryHelperClass();

        return new $helperClass();        
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

	static function getDeliveriesMenuItems() : array
	{
		return cache()->remember(
			Str::slug(static::class . 'getDeliveriesMenuItems'),
			3600 * 24,
			function()
			{
				Carbon::setLocale('it');

				$result = [];

				$deliveries = Delivery::gpc()::current()->orderBy('delivery_datetime')->get();

				foreach($deliveries as $delivery)
				{
					if(! $delivery->getDatetime())
						continue;

					$dateName = $delivery->getDatetime()->translatedFormat('l d M');

					if(! isset($result[$dateName]))
						$result[$dateName] = [
							'name' => 'deliveryMaster' . $delivery->getKey(),
							'text' => $dateName,
							'children' => []
						];

					$result[$dateName]['children'][] = [
						'name' => 'delivery' . $delivery->getKey(),
						'text' => $delivery->getShortName(),
						'href' => $delivery->getShowUrl()
					];
				}

				return $result;
			}
		);
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