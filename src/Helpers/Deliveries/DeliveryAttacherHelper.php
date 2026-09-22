<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Products\Models\OrderProduct;
use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryPartialLogger;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Interfaces\DeliverableInterface;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

use function dd;
use function is_null;
use function view;

class DeliveryAttacherHelper
{
	public Delivery $delivery;
	public array|Collection $elements = [];

	public function setElements(array|Collection $elements) : static
	{
		$this->elements = $elements;

		return $this;
	}

	public function getElementsIds() : Collection
	{
		return $this->getElements()->pluck('id');
	}

	public function getResponse()
	{
		return view('datatables::utilities.closeIframe', [
			'reloadRows' => $this->getElementsIds(),
		]);
	}

	public function attach() : static
	{
		foreach($this->getElements() as $element)
			$this->attachElement($element);

		return $this;
	}

	public function getElements() : array|Collection
	{
		return $this->elements;
	}

	public function setDelivery(Delivery $delivery) : static
	{
		$this->delivery = $delivery;

		return $this;
	}

	public function createContentDeliveryByContent(
		DeliverableInterface $contentDeliveryContent
	) : ContentDelivery
	{
		$contentDeliveryContent->deliveries()->syncWithoutDetaching([
			$this->getDelivery()->getKey()
		]);
	}

	public function provideContentDeliveryByUnitload(Unitload $unitload) : ContentDelivery
	{
		$content = $unitload->getProduction()->getContentForDelivery();

		if($result = $this->getDelivery()->contentDeliveries()
			->byContent($content)
			->first())
			return $result;

		$content->deliveries()->syncWithoutDetaching([$this->getDelivery()->getKey()]);

		return $this->getDelivery()->contentDeliveries()
			->byContent($content)
			->first();
	}

	public function checkOrderProductShippingIntegrity(OrderProduct $orderProduct)
	{
		$this->checkOrderProductShippingTotalQuantity($orderProduct);
	}

	public function checkOrderProductShippingTotalQuantity(OrderProduct $orderProduct)
	{
		$nonPartialDeliveries = $orderProduct->deliveries->where('pivot.partial', false);

		if($nonPartialDeliveries->count() > 1)
		{
			foreach($orderProduct->deliveries as $delivery)
			{
				ContentDeliveryPartialLogger::logTransition(
					$delivery->pivot,
					true,
					'multiple_non_partial_deliveries_for_order_product',
					[
						'writer' => __METHOD__,
						'order_product_id' => $orderProduct->getKey(),
						'non_partial_delivery_ids' => $nonPartialDeliveries->pluck('id')->values()->all(),
						'all_delivery_ids' => $orderProduct->deliveries->pluck('id')->values()->all(),
					]
				);

				$delivery->pivot->partial = true;
				$delivery->pivot->saveQuietly();
			}
		}

		if($orderProduct->deliveries->sum('quantity_required') > $orderProduct->getClientQuantity())
			dd('troppo carico');
	}

	//gettters
	public function getDelivery() : Delivery
	{
		return $this->delivery;
	}

	public function attachContent($content, bool $partial = null, float $quantity = null) : ContentDelivery
	{
		if(is_null($quantity))
			$content->deliveries()->sync([$this->getDelivery()->getKey()]);
		else
			$content->deliveries()->syncWithoutDetaching([$this->getDelivery()->getKey()]);

		$contentDelivery = $content->contentDeliveries()->byDelivery(
			$this->getDelivery()
		)->first();

		if(! is_null($partial))
			$contentDelivery->partial = $partial;

		if(! is_null($quantity))
			$contentDelivery->quantity_required = $quantity;

		$contentDelivery->save();

		return $contentDelivery;
	}

}
