<?php

namespace IlBronza\Warehouse\Helpers\ContentDeliveries;

use Carbon\Carbon;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadLoaderHelper;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;

class ContentDeliveryLoaderHelper extends ContentDeliveryBaseLoaderHelper
{
	static $classConfigPrefix = 'loaderHelper';

	static string $cantExecuteError = 'cantBeLoaded';

	public function getMissingQuantity() : int
	{
		return $this->getContentDelivery()->getQuantityRequired() - $this->getContentDelivery()->getQuantityProduced();
	}

	public function getQuantityTolerance() : int
	{
		return $this->getContentDelivery()->getQuantityRequired() * $this->getTolerance();
	}

	public function getTolerance() : float
	{
		return $this->getContentDelivery()->getLoadingTolerance();
	}

	public function canExecute() : bool
	{
		$unitloads = $this->getContentDelivery()->getUnitloads();

		foreach($unitloads as $unitload)
			if(! $unitload->isCompleted())
			{
				$this->addMessage(trans('warehouse::errors.unitloadNotCompleted',
					[
						'unitload' => $unitload->getName()
					]));

				return false;
			}

		if(($missing = $this->getMissingQuantity()) > ($tolerance = $this->getQuantityTolerance()))
		{
			$this->addMessage(trans('warehouse::errors.cannotLoadContentDeliveryQuantityToleranceExceeded',
				[
					'contentDelivery' => $this->getContentDelivery()->getName(),
					'missingQuantity' => abs($missing),
					'tolerance' => $tolerance
				]));

			return false;
		}

		return true;
	}

	public function __execute() : bool
	{
		$completeSuccess = true;

		$contentDelivery = $this->getContentDelivery();

		foreach($contentDelivery->getUnitloads() as $unitload)
			if(! UnitloadLoaderHelper::gpc()::execute($unitload))
			{
				$this->addMessage(UnitloadLoaderHelper::gpc()::getMessagesBagString($unitload));

				$completeSuccess = false;
			}

		$contentDelivery->loaded_at = Carbon::now();
		$contentDelivery->save();

		if(! $completeSuccess)
			$this->addMessage(trans('warehouse::errors.contentDeliveryHasBeenLoadedButSomeUnitloadsGaveProblems',
				[
					'contentDelivery' => $this->getContentDelivery()->getName(),
				]));

		return $completeSuccess;
	}

}