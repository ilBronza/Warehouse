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
		if(! $quantityPerPacking = $parameters['quantity_capacity'] ?? $loadable->getQuantityPerUnitload())
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
				if(! $contentDelivery = $removable->pluck('contentDelivery')->unique()->filter()->sortByDesc('delivery.delivery_datetime')->first())
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

	static function provideByModelsQuantity(
		UnitloadableInterface $loadable,
		$productionModel,
		float $quantityRequired = null,
		array $parameters = [],
		Processing $processing = null,
		bool $force = false) : Collection
	{
		if(! $productionModel)
			return collect();

		if(! $quantityRequired)
			$quantityRequired = 0;

		if(! $quantityPerPacking = $parameters['quantity_capacity'] ?? $loadable->getQuantityPerUnitload())
			throw new \Exception ('Quantity per packing not defined');

		if(($quantityRequired !== 1)&&($quantityRequired / $quantityPerPacking > 90))
			throw new \Exception ("Quantity required too high, {$quantityPerPacking} per packing is maybe too small for {$quantityRequired} required? Maximum 60 unitloads allowed, call Davide the greatest, the best, the most handsome");
		else if(($quantityRequired == 1)&&($quantityRequired / $quantityPerPacking > 20))
			throw new \Exception ("Quantity required too high, {$quantityPerPacking} per packing is maybe too small for {$quantityRequired} required? Maximum 60 unitloads allowed, call Davide the greatest, the best, the most handsome");

		$productionUnitloads = $productionModel->getProductionUnitloads();

		if((! $productionModel->isCompleted())||($force))
		{
			if($productionUnitloads->sum('quantity') > $quantityRequired)
				return static::removeQuantityOnExistingUnitloads($productionUnitloads, $quantityRequired);

			$parameters['sequence'] = $productionUnitloads->max('sequence') + 1;

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
			
				UnitloadDeliveryCheckerHelper::gpc()::checkForDeliveryAutoAttaching($unitload);
			}
		}
		else
			Ukn::e('La lavorazione risulta completata, impossibile creare nuovi bindelli');

		return $productionUnitloads;
	}

	static function addByModelsQuantity(
		UnitloadableInterface $loadable,
		$productionModel,
		float $quantityRequired,
		array $parameters = [],
		Processing $processing = null,
		bool $force = false
	) : Collection
	{
		$quantityRequired += $productionModel->getProductionUnitloadsQuantity();

		return static::provideByModelsQuantity(
			$loadable,
			$productionModel,
			$quantityRequired,
			$parameters,
			$processing,
			$force
		);
	}

	/**
	 * Ricalcola la distribuzione degli unitload incompleti sulla base della quantità richiesta.
	 * Gli unitload completati (stampati) non vengono modificati.
	 * Gli incompleti vengono ridistribuiti/riempiti/consolidati per evitare "bancalini a metà".
	 *
	 * @return Collection I unitload della produzione dopo il ricalcolo
	 */
	static function recalculateByModelsQuantity(
		UnitloadableInterface $loadable,
		$productionModel,
		float $quantityRequired = null,
		array $parameters = [],
		Processing $processing = null
	) : Collection {
		if (! $productionModel)
			return collect();

		if (! $quantityRequired)
			$quantityRequired = 0;

		$quantityPerPacking = $parameters['quantity_capacity'] ?? $loadable->getQuantityPerUnitload();
		if (! $quantityPerPacking)
			throw new \Exception('Quantity per packing not defined');

		if ($quantityRequired / $quantityPerPacking > 60)
			throw new \Exception("Quantity required too high, maximum 60 unitloads allowed");

		$productionUnitloads = $productionModel->getProductionUnitloads();
		$completed = $productionUnitloads->filter(fn($u) => $u->isCompleted());
		$incomplete = $productionUnitloads->filter(fn($u) => ! $u->isCompleted());

		$completedQty = $completed->sum('quantity');
		$remaining = $quantityRequired - $completedQty;

		// Quantità in eccesso: rimuovi dagli incompleti
		if ($remaining < 0) {
			return static::removeQuantityOnExistingUnitloads($productionUnitloads, $quantityRequired);
		}

		// Nessuna quantità da distribuire sugli incompleti: consolidamento per evitare bancalini sparsi
		if ($remaining == 0) {
			static::redistributeIncompleteUnitloads($incomplete, 0, $quantityPerPacking);
			$productionModel->unsetRelation('productionUnitloads');
			return $productionModel->getProductionUnitloads();
		}

		// Distribuzione ottimale: N pieni + 1 parziale (se serve). L'ultimo può contenere fino al 10% in più.
		[$optimalCount, $targetQuantities] = static::computeOptimalDistribution($remaining, $quantityPerPacking);

		// Ordina per sequence: teniamo gli ultimi N (sequence più alta) così il bindello parziale va per ultimo
		$incompleteSorted = $incomplete->sortBy(fn($u) => $u->sequence ?? 0);

		if ($incompleteSorted->count() >= $optimalCount) {
			// Teniamo gli ultimi N (sequence più alta), cancelliamo i primi; il parziale va all'ultimo
			$toModify = $incompleteSorted->slice(-$optimalCount)->values();
			$toEmpty = $incompleteSorted->slice(0, $incompleteSorted->count() - $optimalCount);

			foreach ($toModify as $i => $unitload) {
				$unitload->quantity = $targetQuantities[$i] ?? $quantityPerPacking;
				$unitload->quantity_expected = $unitload->quantity;
				$unitload->save();
			}

			foreach ($toEmpty as $unitload) {
				$unitload->delete();
			}

			$productionModel->unsetRelation('productionUnitloads');
		} else {
			// Pochi incompleti: riempi prima, poi crea i nuovi
			$toFill = $incompleteSorted->values();
			$filledCount = 0;

			foreach ($toFill as $i => $unitload) {
				$qty = $targetQuantities[$i] ?? $quantityPerPacking;
				$unitload->quantity = $qty;
				$unitload->quantity_expected = $qty;
				$unitload->save();
				$filledCount++;
			}

			$parameters['sequence'] = ($productionUnitloads->max('sequence') ?? 0) + 1;
			$productionModel->unsetRelation('productionUnitloads');
			$productionUnitloads = $productionModel->getProductionUnitloads();

			for ($i = $filledCount; $i < $optimalCount; $i++) {
				$qty = $targetQuantities[$i] ?? $quantityPerPacking;
				$unitloadParameters = static::buildArrayParameters(
					$loadable,
					$productionModel,
					$qty,
					$parameters,
					$processing
				);
				$parameters['sequence']++;
				$unitload = static::createByArray($unitloadParameters, false);
				UnitloadDeliveryCheckerHelper::gpc()::checkForDeliveryAutoAttaching($unitload);
			}
		}

		return $productionModel->getProductionUnitloads();
	}

	/**
	 * Calcola la distribuzione ottimale: l'ultimo bancale può contenere fino al 10% in più
	 * per ridurre il numero totale di bancali.
	 *
	 * @return array{0: int, 1: array<float>} [optimalCount, targetQuantities]
	 */
	protected static function computeOptimalDistribution(float $totalQuantity, float $quantityPerPacking) : array
	{
		$optimalFullCount = (int) floor($totalQuantity / $quantityPerPacking);
		$remainder = $totalQuantity - ($optimalFullCount * $quantityPerPacking);
		$maxExtraOnLast = $quantityPerPacking * 0.1;

		if ($remainder > 0 && $optimalFullCount >= 1 && $remainder <= $maxExtraOnLast) {
			$optimalCount = $optimalFullCount;
			$targetQuantities = array_merge(
				array_fill(0, $optimalFullCount - 1, $quantityPerPacking),
				[$quantityPerPacking + $remainder]
			);
		} else {
			$optimalCount = $optimalFullCount + ($remainder > 0 ? 1 : 0);
			$targetQuantities = array_merge(
				array_fill(0, $optimalFullCount, $quantityPerPacking),
				$remainder > 0 ? [$remainder] : []
			);
		}

		return [$optimalCount, $targetQuantities];
	}

	/**
	 * Ridistribuisce la quantità sugli unitload incompleti quando remaining == 0
	 * (consolidamento per evitare molti bancalini parziali).
	 */
	protected static function redistributeIncompleteUnitloads(
		Collection $incomplete,
		float $remaining,
		float $quantityPerPacking
	) : void {
		if ($incomplete->isEmpty())
			return;

		$incompleteQty = $incomplete->sum('quantity');
		$totalToDistribute = $remaining + $incompleteQty;

		if ($totalToDistribute <= 0) {
			foreach ($incomplete as $unitload) {
				$unitload->delete();
			}
			return;
		}

		[$optimalCount, $targetQuantities] = static::computeOptimalDistribution($totalToDistribute, $quantityPerPacking);

		// Ordina per sequence: teniamo gli ultimi N così il bindello parziale va per ultimo
		$incompleteSorted = $incomplete->sortBy(fn($u) => $u->sequence ?? 0);

		$toModify = $incompleteSorted->slice(-$optimalCount)->values();
		$toEmpty = $incompleteSorted->slice(0, $incompleteSorted->count() - $optimalCount);

		foreach ($toModify as $i => $unitload) {
			$unitload->quantity = $targetQuantities[$i] ?? $quantityPerPacking;
			$unitload->quantity_expected = $unitload->quantity;
			$unitload->save();
		}

		foreach ($toEmpty as $unitload) {
			$unitload->delete();
		}
	}

}