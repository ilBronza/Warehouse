<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use App\Processing;
use IlBronza\Products\Models\Product\Product;
use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Models\Interfaces\UnitloadableInterface;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use function array_merge;
use function collect;

class UnitloadCreatorHelper
{
	public Unitload $unitload;
	public array $parameters;

	public function __construct()
	{
		$this->makeUnitload();
	}

	public function makeUnitload()
	{
		$this->unitload = Unitload::gpc()::make();
	}

	public function saveUnitload()
	{
		$this->getUnitload()->save();
	}

	public function getUnitload() : Unitload
	{
		return $this->unitload;
	}

	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
	}

	public function setParameter(string $name, mixed $value)
	{
		$this->parameters[$name] = $value;
	}

	public function unsetParameter(string $name)
	{
		unset($this->parameters[$name]);
	}

	public function getParameters() : array
	{
		return $this->parameters;
	}

	public function getParameter(string $parameterName) : mixed
	{
		if (! isset($this->parameters[$parameterName]))
			return null;

		return $this->parameters[$parameterName];
	}

	public function associateProductionRelationship()
	{
		if (! $production = $this->getParameter('production'))
			return;

		$this->unsetParameter('production');

		$this->getUnitload()->production()->associate($production);

		if ($this->getParameter('order_product_id'))
			return;

		if (! $production instanceof Model)
			return;

		if (! method_exists($production, 'getOrderProduct'))
			return;

		if (! $orderProduct = $production->getOrderProduct())
			return;

		$this->setParameter('order_product_id', $orderProduct->getKey());
	}

	public function associateLoadableRelationship()
	{
		if (! $loadable = $this->getParameter('loadable'))
			return;

		$this->getUnitload()->loadable()->associate($loadable);

		$this->unsetParameter('loadable');

		if (! $loadable instanceof Product)
			return;

		if ($this->getParameter('product_id'))
			return;

		$this->setParameter('product_id', $loadable->getKey());
	}

	public function associateRelationships()
	{
		$this->associateProductionRelationship();
		$this->associateLoadableRelationship();
	}

	public function bindParameters()
	{
		$unitload = $this->getUnitload();

		foreach ($this->getParameters() as $name => $value)
			$unitload->$name = $value;
	}

	static function validateParameters(array $parameters) : array
	{
		if (($parameters['quantity_expected'] ?? 0) < 0)
			$parameters['quantity_expected'] = null;

		if (($parameters['quantity'] ?? 0) < 0)
			$parameters['quantity'] = null;

		return $parameters;
	}

	static function createByArray(array $parameters, bool $checkDelivery = true) : Unitload
	{
		$parameters = static::validateParameters($parameters);

		$helper = new static();

		$helper->setParameters($parameters);

		$helper->associateRelationships();
		$helper->bindParameters();

		$helper->saveUnitload();

		$unitload = $helper->getUnitload();

		if($checkDelivery)
			UnitloadDeliveryCheckerHelper::gpc()::checkForDeliveryAutoAttaching($unitload);

		return $unitload;
	}

	static function createPlaceholder(array $parameters = []) : Unitload
	{
		$parameters['placeholder'] = true;

		return static::createByArray($parameters);
	}

	static function buildArrayParameters(
		UnitloadableInterface $loadable,
		$productionModel,
		float $quantityRequired,
		array $parameters = [],
		Processing $processing = null
	) : array
	{
		if(! $quantityPerPacking = $loadable->getQuantityPerUnitload())
			throw new \Exception ('Quantity per packing not defined');

		if($quantityRequired / $quantityPerPacking > 60)
			throw new \Exception ('Quantity required too high, maximum 60 unitloads allowed');

		$result = [
			'production' => $productionModel,
			'loadable' => $loadable,
			'quantity_capacity' => $quantityPerPacking,
			'quantity_expected' => $quantityRequired,
			'quantity' => $quantityRequired,
			'user_id' => Auth::id(),
			'processing_id' => $processing?->getKey(),
		];

		if(! isset($parameters['placeholder']))
			$parameters['placeholder'] = true;

		return array_merge(
			$parameters,
			$result
	);
	}

	static function removeQuantityOnExistingUnitloads(Collection $productionUnitloads, float $quantityRequired)
	{
		$removingQuantity = $productionUnitloads->sum('quantity') - $quantityRequired;

		$removable = $productionUnitloads->filter(function($item)
		{
			return ! $item->isCompleted();
		});

		$removableQuantity = $removable->sum('quantity');

		if($removingQuantity > $removableQuantity)
		{
			Ukn::e('Impossibile rimuovere ' . $removingQuantity . ' pezzi dalla produzione avvenuta (' . $removingQuantity - $removableQuantity . ' pezzi prodotti non rimuovibili)');

			return $productionUnitloads;
		}

		while ($removingQuantity > 0)
		{
			if(! ($first = $removable->whereNull('content_delivery_id')->sortBy('quantity')->first()))
			{
				if(! $contentDelivery = $removable->pluck('contentDelivery')->unique()->filter()->sortByDesc('delivery.datetime')->first())
					dd($removable);

				if(! ($first = $removable->where('content_delivery_id', $contentDelivery->getKey())->sortBy('quantity')->first()))
					dd('problema');
			}

			if($removingQuantity >= $first->getQuantity())
			{
				$removingQuantity -= $first->getQuantity();
				$removable = $removable->reject(fn($item) => $item->id === $first->id);
				$productionUnitloads = $productionUnitloads->reject(fn($item) => $item->id === $first->id);
				$first->delete();

				continue;
			}

			$first->quantity -= $removingQuantity;
			$first->save();

			$removingQuantity = 0;
		}

		return $productionUnitloads;
	}

	static function provideByModelsQuantity(UnitloadableInterface $loadable, $productionModel, float $quantityRequired = null, array $parameters = [], Processing $processing = null) : Collection
	{
		if(! $quantityRequired)
			$quantityRequired = 0;

		if(! $quantityPerPacking = $loadable->getQuantityPerUnitload())
			throw new \Exception ('Quantity per packing not defined');

		if(($quantityRequired !== 1)&&($quantityRequired / $quantityPerPacking > 90))
			throw new \Exception ("Quantity required too high, {$quantityPerPacking} per packing is maybe too small for {$quantityRequired} required? Maximum 60 unitloads allowed, call Davide the greatest, the best, the most handsome");
		else if(($quantityRequired == 1)&&($quantityRequired / $quantityPerPacking > 20))
			throw new \Exception ("Quantity required too high, {$quantityPerPacking} per packing is maybe too small for {$quantityRequired} required? Maximum 60 unitloads allowed, call Davide the greatest, the best, the most handsome");

		$productionUnitloads = $productionModel->getProductionUnitloads();

		// if(! $productionModel->isCompleted())
		// {
			if($productionUnitloads->sum('quantity') > $quantityRequired)
				return static::removeQuantityOnExistingUnitloads($productionUnitloads, $quantityRequired);

			$parameters['sequence'] = $productionUnitloads->max('sequence') + 1;

			$created = collect();

			while (($remaining = $quantityRequired - $productionModel->getProductionUnitloadsQuantity()) > 0)
			{
				$quantity = $remaining > $quantityPerPacking ? $quantityPerPacking : $remaining;

				$unitloadParameters = static::buildArrayParameters(
					$loadable,
					$productionModel,
					$quantity,
					$parameters,
					$processing
				);

				$parameters['sequence'] ++;

				$productionUnitloads->push(
					$unitload = UnitloadCreatorHelper::createByArray($unitloadParameters, false)
				);

				$created->push($unitload);
			}

			UnitloadDeliveryCheckerHelper::gpc()::checkForDeliveryAutoAttaching($created);
		// }

		return $productionUnitloads;
	}

	static function addByModelsQuantity(UnitloadableInterface $loadable, $productionModel, float $quantityRequired, array $parameters = [], Processing $processing = null) : Collection
	{
		$quantityRequired += $productionModel->getProductionUnitloadsQuantity();

		return static::provideByModelsQuantity(
			$loadable,
			$productionModel,
			$quantityRequired,
			$parameters,
			$processing
		);
	}

}