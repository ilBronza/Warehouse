<?php

namespace IlBronza\Warehouse\Helpers;

use IlBronza\Products\Models\Product\Product;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Model;

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

	static function createByArray(array $parameters) : Unitload
	{
		$parameters = static::validateParameters($parameters);

		$helper = new static();

		$helper->setParameters($parameters);

		$helper->associateRelationships();
		$helper->bindParameters();

		$helper->saveUnitload();

		return $helper->getUnitload();
	}


	static function createPlaceholder(array $parameters = []) : Unitload
	{
		$parameters['placeholder'] = true;

		return static::createByArray($parameters);
	}
}