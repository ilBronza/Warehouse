<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

use function collect;

class DatatableFieldGroupedContentDelivery extends DatatableFieldEach
{
	public ? string $translationPrefix = 'warehouse::fields';
	public $childParameters = [
		'type' => 'flat',
	];

	public function transformValue($value)
	{
		try
		{
			return $value->map(function ($item)
			{
				return $this->getItemValue($item);
			});
		}
		catch (\Throwable $e)
		{
			if($this->debug())
				throw $e;

			return collect();
		}
	}

	public function getItemValue($item)
	{
		return $this->_transformValue($item);
	}
}