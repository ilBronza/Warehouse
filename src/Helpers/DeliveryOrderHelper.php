<?php

namespace IlBronza\Warehouse\Helpers;

use IlBronza\Products\Models\Order;
use IlBronza\Products\Models\OrderProduct;
use Illuminate\Support\Collection;

use function dd;

class DeliveryOrderHelper extends DeliveryAttacherHelper
{
	public array|Collection $orders = [];

	public function setOrders(array|Collection $orders) : static
	{
		$this->orders = $orders;

		return $this;
	}

	public function getOrders() : array|Collection
	{
		return $this->orders;
	}

	public function attachOrderProduct(OrderProduct $orderProduct, bool $partial = false, float $quantity = null)
	{
		$contentDelivery = $this->_attachTarget($orderProduct, $partial, $quantity);

		$unitloads = $orderProduct->getUnitloadsByClientQuantity();

		$this->checkOrderProductShippingIntegrity($orderProduct, $unitloads);

		ContentDeliveryUnitloadsHelper::addUnitloadsToContentDelivery($contentDelivery, $unitloads);

	}

	public function attachOrder(Order $order)
	{
		foreach($order->getOrderProducts() as $orderProduct)
			$this->attachOrderProduct($orderProduct);
	}

	public function attach()
	{
		foreach($this->getOrders() as $order)
			$this->attachOrder($order);
	}

	public function getOrdersIds() : Collection
	{
		return $this->getOrders()->pluck('id');
	}

	public function checkOrderProductShippingIntegrity(OrderProduct $orderProduct)
	{
		$this->checkOrderProductShippingTotalQuantity($orderProduct);
	}

	public function checkOrderProductShippingTotalQuantity(OrderProduct $orderProduct)
	{
		if($orderProduct->deliveries->where('pivot.partial', false)->count() > 1)
			dd('troppe intere');

		if($orderProduct->deliveries->sum('quantity_required') > $orderProduct->getClientQuantity())
			dd('troppo carico');
	}

	public function getResponse()
	{
		return view('datatables::utilities.closeIframe', [
			'reloadRows' => $this->getOrdersIds(),
		]);
	}
}