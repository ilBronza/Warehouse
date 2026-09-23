<?php

namespace IlBronza\Warehouse\Helpers\ContentDeliveries;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Interfaces\DeliverableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ContentDeliveryFullyDeliveredHelper
{
	/**
	 * Una distinta senza fratelli parziali è completa solo se caricata.
	 * Se almeno una distinta dello stesso contenuto è parziale, tutte le sorelle
	 * devono essere caricate, senza unitload non assegnati, e le quantità allocate
	 * devono coprire almeno il 95% del fabbisogno del contenuto.
	 */
	/**
	 * Ricalcola il flag e restituisce le distinte che risultavano incoerenti.
	 *
	 * Se almeno una distinta dello stesso contenuto è parziale, il flag descrive
	 * lo stato complessivo del contenuto e deve quindi avere lo stesso valore su
	 * tutte le distinte sorelle.
	 */
	public static function check(ContentDelivery $contentDelivery, bool $persist = true) : Collection
	{
		$content = $contentDelivery->getContent();

		if (! $content)
			return static::persistFullyDeliveredAll(
				collect([$contentDelivery]),
				false,
				$persist
			);

		$siblings = static::siblingsForContent($content);

		if (! $siblings->contains(fn (ContentDelivery $sibling) => $sibling->isPartial()))
			return static::persistFullyDeliveredAll(
				collect([$contentDelivery]),
				$contentDelivery->isLoaded(),
				$persist
			);

		foreach ($siblings as $sibling)
			if (! $sibling->isLoaded())
				return static::persistFullyDeliveredAll($siblings, false, $persist);

		$totalSent = $siblings->sum(fn(ContentDelivery $cd) => $cd->getAllocatedQuantity());

		$required = static::contentShipmentQuantityRequired($content);

		$unitloadsWithoutContentDelivery = static::getUnitloadsWithoutContentDelivery($content);

		if ($unitloadsWithoutContentDelivery->isNotEmpty())
			return static::persistFullyDeliveredAll($siblings, false, $persist);

		return static::persistFullyDeliveredAll(
			$siblings,
			$totalSent >= $required * 0.95,
			$persist
		);
	}

	protected static function persistFullyDeliveredAll(
		Collection $siblings,
		bool $value,
		bool $persist
	) : Collection
	{
		$inconsistentContentDeliveries = $siblings->filter(
			fn (ContentDelivery $contentDelivery) => $contentDelivery->fully_delivered !== $value
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

	/**
	 * Unitload del content ancora senza distinta (content_delivery_id nullo).
	 * Usa la relazione unitloads()->notDelivering() sul deliverable (stesso criterio di hasUndeliveringUnitloads).
	 */
	public static function getUnitloadsWithoutContentDelivery(?DeliverableInterface $content) : Collection
	{
		return $content->unitloads()->notDelivering()->orderBy('sequence')->get();
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
			->whereNull('deleted_at')
			->with(['delivery', 'unitloads'])
			->orderBy('sorting_index')
			->get();
	}
}
