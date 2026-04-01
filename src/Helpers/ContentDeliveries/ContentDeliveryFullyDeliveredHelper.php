<?php

namespace IlBronza\Warehouse\Helpers\ContentDeliveries;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Interfaces\DeliverableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ContentDeliveryFullyDeliveredHelper
{
	/**
	 * partial = false/null: fully_delivered = true e stop.
	 * partial = true: tutti i content_delivery fratelli (stesso content) devono essere caricati (isLoaded);
	 * poi somma quantità allocate vs fabbisogno del content; se ok, fully_delivered = true su tutti i fratelli, altrimenti false.
	 */
	public static function check(ContentDelivery $contentDelivery) : void
	{
		if (! $contentDelivery->isPartial())
		{
			$contentDelivery->fully_delivered = true;
			$contentDelivery->save();

			return;
		}

		$content = $contentDelivery->getContent();

		$siblings = static::siblingsForContent($content);

		foreach ($siblings as $sibling)
			if (! $sibling->isLoaded())
			{
				static::persistFullyDeliveredAll($siblings, false);
				return;
			}

		$totalSent = $siblings->sum(fn(ContentDelivery $cd) => $cd->getAllocatedQuantity());

		$required = static::contentShipmentQuantityRequired($content);

		static::persistFullyDeliveredAll($siblings, $totalSent >= $required * 0.95);
	}

	protected static function persistFullyDeliveredAll(Collection $siblings, bool $value) : void
	{
		foreach ($siblings as $contentDelivery)
		{
			$contentDelivery->fully_delivered = $value;
			$contentDelivery->save();
		}
	}

	protected static function contentShipmentQuantityRequired(Model $content) : float
	{
		return $content->getQuantityRequired();
	}

	protected static function siblingsForContent(DeliverableInterface $content) : Collection
	{
		return ContentDelivery::gpc()::query()
			->where('content_type', $content->getMorphClass())
			->where('content_id', $content->getKey())
			->with(['delivery', 'unitloads'])
			->orderBy('sorting_index')
			->get();
	}
}
