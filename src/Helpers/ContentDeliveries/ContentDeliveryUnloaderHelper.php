<?php

namespace IlBronza\Warehouse\Helpers\ContentDeliveries;

use Carbon\Carbon;
use IlBronza\CRUD\Traits\Helpers\HelperMessageBagTrait;
use IlBronza\CRUD\Traits\PackagedHelpersTrait;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadLoaderHelper;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;

use Illuminate\Database\Eloquent\Model;

use function abs;
use function dd;
use function trans;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadUnloaderHelper;

class ContentDeliveryUnloaderHelper extends ContentDeliveryBaseLoaderHelper
{
	static $classConfigPrefix = 'unloaderHelper';

	static string $cantExecuteError = 'cantBeUnloaded';

	public function canExecute() : bool
	{
		return true;
	}

	public function __execute() : bool
	{
		$completeSuccess = true;

		$contentDelivery = $this->getContentDelivery();

		foreach($contentDelivery->getUnitloads() as $unitload)
			if(! UnitloadUnloaderHelper::gpc()::execute($unitload))
			{
				$this->addMessage(UnitloadUnloaderHelper::gpc()::getMessagesBagString($unitload));

				$completeSuccess = false;
			}

		$contentDelivery->loaded_at = null;
		$contentDelivery->save();

		if(! $completeSuccess)
			$this->addMessage(trans('warehouse::errors.contentDeliveryHasBeenUnloadedButSomeUnitloadsGaveProblems',
				[
					'contentDelivery' => $this->getContentDelivery()->getName(),
				]));

		return $completeSuccess;
	}
}