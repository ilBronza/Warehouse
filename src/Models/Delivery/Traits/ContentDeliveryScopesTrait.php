<?php

namespace IlBronza\Warehouse\Models\Delivery\Traits;

use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Interfaces\DeliverableInterface;

trait ContentDeliveryScopesTrait
{
	public function scopeByDelivery($query, string|Delivery $delivery)
	{
		if (! is_string($delivery))
			$delivery = $delivery->getKey();

		return $query->where('delivery_id', $delivery);
	}

	public function scopeByContent($query, DeliverableInterface $content)
	{
		return $query->where('content_type', $content->getMorphClass())
			->where('content_id', $content->getKey());
	}

	public function scopeSortedByClient($query)
	{
		return $query->whereHas('content.order', function($_query)
		{
			$_query->orderBy('client_id');
		});
	}

	public function scopeLoaded($query)
	{
		return $query->whereNotNull('loaded_at');
	}

	public function scopeNotLoaded($query)
	{
		return $query->whereNull('loaded_at');
	}

	public function scopePartial($query)
	{
		return $query->where('partial', true);
	}

	public function scopeNotPartial($query)
	{
		return $query->where(function($q)
		{
			$q->whereNull('partial')->orWhere('partial', false);
		});
	}

	public function scopeFullyDelivered($query)
	{
		return $query->where('fully_delivered', true);
	}

	public function scopeNullFullyDelivered($query)
	{
		return $query->whereNull('fully_delivered');
	}

	public function scopeNotFullyDelivered($query)
	{
		return $query->where(function($q)
		{
			$q->whereNull('fully_delivered')->orWhere('fully_delivered', false);
		});
	}

	/** warned diversa da null e da 0 (1 = da avvisare, 2 = avvisato, ecc.) */
	public function scopeWithWarning($query)
	{
		return $query->whereNotNull('warned')->where('warned', '!=', 0);
	}

	public function scopeWithoutWarning($query)
	{
		return $query->where(function($q)
		{
			$q->whereNull('warned')->orWhere('warned', 0);
		});
	}

	public function scopeWarningToHandle($query)
	{
		return $query->where('warned', 1);
	}

	public function scopeWarningHandled($query)
	{
		return $query->where('warned', 2);
	}
}
