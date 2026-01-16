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

abstract class ContentDeliveryBaseLoaderHelper
{
	use PackagedHelpersTrait;
	use HelperMessageBagTrait;

	public ContentDelivery $contentDelivery;

	static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'contentDelivery';


	abstract public function canExecute() : bool;
	abstract public function __execute() : bool;


	public function __construct(ContentDelivery $contentDelivery)
	{
		$this->contentDelivery = $contentDelivery;
	}

	static function execute(ContentDelivery $contentDelivery) : bool
	{
		$result = new static($contentDelivery);

		return $result->_execute();
	}

	public function _execute() : bool
	{
		if(! $this->canExecute())
		{
			$this->addMessage(trans('warehouse::errors.' . static::$cantExecuteError,
				[
					'contentDelivery' => $this->getContentDelivery()->getName(),
				]));

			return false;
		}

		return $this->__execute();
	}


	public function getSubjectModel() : Model
	{
		return $this->getContentDelivery();
	}

	public function getContentDelivery() : ContentDelivery
	{
		return $this->contentDelivery;
	}

}