<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use Carbon\Carbon;
use IlBronza\CRUD\Traits\Helpers\HelperMessageBagTrait;
use IlBronza\CRUD\Traits\PackagedHelpersTrait;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Model;

use function trans;

class UnitloadUnloaderHelper extends UnitloadBaseLoaderHelper
{
	static $classConfigPrefix = 'unloaderHelper';

	public function canExecute() : bool
	{
		if(! $this->getUnitload()->isLoaded())
		{
			$this->addMessage(trans('warehouse::errors.unitloadAlreadyUnloaded',
				[
					'unitload' => $this->getUnitload()->getName()
				]));

			return false;
		}

		return true;
	}

	public function __execute() : bool
	{
		$this->getUnitload()->setLoadedAt(null, true);

		return true;
	}
}