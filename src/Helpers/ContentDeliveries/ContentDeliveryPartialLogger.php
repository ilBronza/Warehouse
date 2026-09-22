<?php

namespace IlBronza\Warehouse\Helpers\ContentDeliveries;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContentDeliveryPartialLogger
{
	public static function logTransition(
		ContentDelivery $contentDelivery,
		bool $value,
		string $reason,
		array $context = []
	) : void
	{
		$previousValue = $contentDelivery->getRawOriginal('partial');

		if((bool) $previousValue === $value)
			return;

		$context = array_merge([
			'content_delivery_id' => $contentDelivery->getKey(),
			'delivery_id' => $contentDelivery->delivery_id,
			'content_type' => $contentDelivery->content_type,
			'content_id' => $contentDelivery->content_id,
			'partial_from' => $previousValue,
			'partial_to' => $value,
			'reason' => $reason,
			'user_id' => Auth::id(),
			'route_name' => request()?->route()?->getName(),
			'running_in_console' => app()->runningInConsole(),
		], $context);

		if($value)
			Log::warning('warehouse.content_delivery.partial_changed', $context);
		else
			Log::info('warehouse.content_delivery.partial_changed', $context);
	}
}
