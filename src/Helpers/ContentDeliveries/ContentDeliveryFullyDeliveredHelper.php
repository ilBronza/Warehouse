<?php

namespace IlBronza\Warehouse\Helpers\ContentDeliveries;

use Carbon\Carbon;
use IlBronza\Products\Models\OrderProduct;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use Illuminate\Support\Collection;

class ContentDeliveryFullyDeliveredHelper
{
	/**
	 * Ricalcola il flag usando soltanto il completamento del prodotto d'ordine
	 * e del relativo ordine. Una distinta può diventare completa solo dopo
	 * due giorni dalla spedizione effettiva.
	 */
	public static function check(ContentDelivery $contentDelivery, bool $persist = true) : Collection
	{
		$content = $contentDelivery->getContent();
		$siblings = static::siblingsForContentDelivery($contentDelivery);

		$fullyDelivered = $content instanceof OrderProduct
			&& $content->isCompleted()
			&& $content->getOrder()?->isCompleted();

		return static::persistFullyDeliveredAll($siblings, $fullyDelivered, $persist);
	}

	public static function shipmentIsAtLeastTwoDaysOld(ContentDelivery $contentDelivery) : bool
	{
		$shippedAt = $contentDelivery->getDelivery()?->getShippedAt();

		return $shippedAt && $shippedAt->lessThanOrEqualTo(Carbon::now()->subDays(2));
	}

	protected static function persistFullyDeliveredAll(
		Collection $siblings,
		bool $value,
		bool $persist
	) : Collection
	{
		$inconsistentContentDeliveries = $siblings->filter(
			fn (ContentDelivery $contentDelivery) => $contentDelivery->fully_delivered !== $value
				&& (! $value || static::shipmentIsAtLeastTwoDaysOld($contentDelivery))
		);

		if (! $persist)
			return $inconsistentContentDeliveries;

		foreach ($inconsistentContentDeliveries as $contentDelivery)
		{
			$contentDelivery->fully_delivered = $value;
			$contentDelivery->save();
		}

		return $inconsistentContentDeliveries;
	}

	protected static function siblingsForContentDelivery(ContentDelivery $contentDelivery) : Collection
	{
		return ContentDelivery::gpc()::query()
			->where('content_type', $contentDelivery->content_type)
			->where('content_id', $contentDelivery->content_id)
			->whereNull('deleted_at')
			->with('delivery')
			->orderBy('sorting_index')
			->get();
	}
}
