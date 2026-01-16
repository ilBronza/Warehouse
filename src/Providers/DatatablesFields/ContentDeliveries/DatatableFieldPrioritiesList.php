<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\ContentDeliveries;

use IlBronza\Datatables\DatatablesFields\DatatableField;
use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldEach;

use IlBronza\Products\Models\Order;

class DatatableFieldPrioritiesList extends DatatableFieldEach
{
	public $childParameters = [
			'type' => 'editor.toggle',
			'editorProperty' => 'priority'
		];

	public function provideChildPlaceholderElement(DatatableField $childField)
	{
		return Order::gpc()::make();
	}

	public function transformValue($value)
	{
		return $value->map(function ($item)
		{
			return $this->getItemValue($item->getContent()->getOrder());
		});
	}
}