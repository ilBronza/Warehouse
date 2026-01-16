<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use Carbon\Carbon;

class UnitloadLoaderHelper extends UnitloadBaseLoaderHelper
{
	static $classConfigPrefix = 'loaderHelper';

	public function canExecute() : bool
	{
		if($this->getUnitload()->isLoaded())
		{
			$this->addMessage(trans('warehouse::errors.unitloadAlreadyLoaded',
				[
					'unitload' => $this->getUnitload()->getName()
				]));

			return false;
		}

		if(! $this->getUnitload()->isCompleted())
		{
			$this->addMessage(trans('warehouse::errors.unitloadNotCompleted',
				[
					'unitload' => $this->getUnitload()->getName()
				]));

			return false;
		}

		return true;
	}

	public function __execute() : bool
	{
		$this->getUnitload()->setLoadedAt(Carbon::now(), true);

		return true;
	}
}