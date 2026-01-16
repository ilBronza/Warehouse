<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

class DatatableFieldContentList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'contentList';

	private function getResultString($value) : string
	{
		return $value->implode(function($item)
		{
			return $item->getContent()->getName() . ' - ' . $item->getContent()->getProduct()->getShortDescription() . ' x ' . $item->getQuantityRequired() . 'pz';
		}, '<br />');
	}

	public function _transformValue($value)
	{
		return "<span uk-tooltip='{$this->getResultString($value)}'> " . count($value) . " </span>";

	}
}