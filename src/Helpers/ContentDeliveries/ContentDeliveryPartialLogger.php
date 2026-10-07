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
		static::writeTransition($contentDelivery, $contentDelivery->getRawOriginal('partial'), $value, $reason, $context);
	}

	public static function logSavedTransition(
		ContentDelivery $contentDelivery,
		$previousValue,
		string $reason,
		array $context = []
	) : void
	{
		static::writeTransition($contentDelivery, $previousValue, $contentDelivery->isPartial(), $reason, $context);
	}

	protected static function writeTransition(
		ContentDelivery $contentDelivery,
		$previousValue,
		bool $value,
		string $reason,
		array $context
	) : void
	{
		if((bool) $previousValue === $value)
			return;

		$explanation = static::explainReason($reason, $context);
		$context = array_merge($context, [
			'content_delivery_id' => $contentDelivery->getKey(),
			'delivery_id' => $contentDelivery->delivery_id,
			'content_type' => $contentDelivery->content_type,
			'content_id' => $contentDelivery->content_id,
			'partial_from' => $previousValue,
			'partial_to' => $value,
			'reason' => $reason,
			'reason_message' => $explanation,
			'quantity_required' => $contentDelivery->getAttributes()['quantity_required'] ?? null,
			'quantity' => $contentDelivery->getAttributes()['quantity'] ?? null,
			'user_id' => Auth::id(),
			'route_name' => request()?->route()?->getName(),
			'running_in_console' => app()->runningInConsole(),
			'stacktrace' => static::captureStacktrace(),
		]);
		$message = sprintf(
			'warehouse.content_delivery.partial_changed: ContentDelivery %s impostata come %s. Motivo: %s',
			$contentDelivery->getKey(),
			$value ? 'parziale' : 'intera',
			$explanation
		);

		if($value)
			Log::warning($message, $context);
		else
			Log::info($message, $context);
	}

	protected static function captureStacktrace() : array
	{
		return array_map(static fn(array $frame) => [
			'file' => $frame['file'] ?? null,
			'line' => $frame['line'] ?? null,
			'class' => $frame['class'] ?? null,
			'function' => $frame['function'] ?? null,
			'type' => $frame['type'] ?? null,
		], debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
	}

	protected static function explainReason(string $reason, array $context) : string
	{
		return match($reason) {
			'unitloads_count_does_not_match_first_production_brothers' => sprintf(
				'Il numero di bindelli associati (%s) è diverso da quello della produzione del primo bindello (%s).',
				$context['unitloads_count'] ?? '?',
				$context['brother_count'] ?? '?'
			),
			'unitloads_count_matches_first_production_brothers' => sprintf(
				'Sono associati tutti i %s bindelli previsti dalla produzione del primo bindello.',
				$context['brother_count'] ?? '?'
			),
			'no_unitloads_attached' => 'Nessun bindello associato: il controllo di integrità ha impostato la consegna come intera.',
			'multiple_non_partial_deliveries_for_order_product' => 'Il prodotto è associato a più consegne intere; il controllo ha impostato le consegne come parziali.',
			'unitload_detached_from_content_delivery' => sprintf(
				'Il bindello %s è stato rimosso dalla consegna.', $context['unitload_id'] ?? '?'
			),
			'unitload_moved_from_content_delivery' => sprintf(
				'Il bindello %s è stato spostato dalla consegna %s alla consegna %s.',
				$context['unitload_id'] ?? '?',
				$context['source_delivery_id'] ?? '?',
				$context['target_delivery_id'] ?? '?'
			),
			'partial_requested_when_attaching_content' => 'Il flag partial è stato richiesto esplicitamente durante l’associazione del materiale alla consegna.',
			'partial_flag_explicitly_updated' => 'Il flag partial è stato modificato direttamente, senza una motivazione applicativa specifica; consultare utente e route nel contesto.',
			default => $reason,
		};
	}
}
