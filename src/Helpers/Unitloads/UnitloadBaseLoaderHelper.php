<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use Carbon\Carbon;
use IlBronza\CRUD\Traits\Helpers\HelperMessageBagTrait;
use IlBronza\CRUD\Traits\PackagedHelpersTrait;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Model;

use function trans;

abstract class UnitloadBaseLoaderHelper
{
	use PackagedHelpersTrait;
	use HelperMessageBagTrait;

	static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'unitload';

	abstract public function canExecute() : bool;
	abstract public function __execute() : bool;


	public Unitload $unitload;

	public function __construct(Unitload $unitload)
	{
		$this->unitload = $unitload;
	}

	public function getSubjectModel() : Model
	{
		return $this->getUnitload();
	}

	public function getUnitload() : Unitload
	{
		return $this->unitload;
	}

	static function execute(Unitload $unitload) : bool
	{
		$helper = new static($unitload);

		return $helper->_execute();
	}

	public function _execute() : bool
	{
		if(! $this->canExecute())
			return false;

		return $this->__execute();
	}


}