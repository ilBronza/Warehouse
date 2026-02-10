<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

class DatatableFieldLoadingList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'loadingList';

	public function _transformValue($value)
	{
		if(! $value)
			return null;

		$loaded = $value->filter(function($item)
		{
			return $item->isLoaded();
		})->count();

		$count = $value->count();
		$content = "{$loaded}/{$count}";

		if($loaded == $count)
			return __('crud::labels.green', ['content' => $content]);
		
		if($count == 0)
			return __('crud::labels.red', ['content' => $content]);
		
		return __('crud::labels.yellow', ['content' => $content]);
	}
}