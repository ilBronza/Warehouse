<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use DOMDocument;
use IlBronza\Products\Models\OrderProduct;

class DatatableFieldProductionList extends DatatableFieldGroupedContentDelivery
{
	public ?string $forcedStandardName = 'productionList';

	public function _transformValue($value)
	{
		if (!$value || $value->isEmpty())
			return null;

		$ready = 0;
		$hasProduction = false;
		$details = [];

		foreach ($value as $item)
		{
			$content = $item->getContent();
			$required = $item->getQuantityRequired();
			$produced = $content ? $item->getQuantityProduced() : 0;

			$target = $required;
			// Per le consegne intere, la produzione chiusa fissa la quantità finale.
			// I pezzi devono comunque essere pronti e assegnati a questa consegna.
			if ($required > 0 && !$item->isPartial() && $content instanceof OrderProduct
				&& $content->isCompleted() && ($finalQuantity = $content->getQuantityDone()) > 0)
				$target = min($required, $finalQuantity);

			if ($content && $target > 0 && $produced >= $target)
				$ready++;

			$description = e($content?->getName() ?? __('warehouse::fields.productionContentUnavailable'));
			$quantities = e(__('warehouse::fields.productionItemQuantities', [
				'produced' => number_format($produced, 0, ',', '.'),
				'required' => $required === null ? '—' : number_format($required, 0, ',', '.'),
			]));
			if ($target < $required)
				$quantities .= '<br>' . e(__('warehouse::fields.productionCompletedQuantity', [
					'quantity' => number_format($target, 0, ',', '.'),
				]));
			$hasStartedPhase = false;
			$status = $this->getProductionDetails($content ? $item->getProductionStatusList() : null, $hasStartedPhase);
			$hasProduction = $hasProduction || $produced > 0 || $hasStartedPhase;
			$details[] = "<strong>{$description}</strong><br>{$quantities}<br>{$status}";
		}

		$total = $value->count();
		$color = $ready === $total ? 'green' : ($hasProduction ? 'yellow' : 'red');
		$summary = __('warehouse::fields.productionReadyCount', ['ready' => $ready, 'total' => $total]);
		$tooltip = e(e($summary) . '<br><br>' . implode('<br><br>', $details));
		$label = __('crud::labels.' . $color, ['content' => "{$ready}/{$total}"]);

		return '<span uk-tooltip title="' . $tooltip . '">' . $label . '</span>';
	}

	private function getProductionDetails(?string $status, bool &$hasStartedPhase) : string
	{
		if (!$status)
			return e(__('warehouse::fields.productionStatusUnavailable'));

		// Riusa gli stati delle lavorazioni, conservando solo testo e colori.
		// Il tooltip non deve riprodurre link o attributi HTML dei dati originali.
		$document = new DOMDocument();
		$document->loadHTML(
			'<html><head><meta charset="UTF-8"></head><body>' . $status . '</body></html>',
			LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
		);

		$phases = [];
		foreach ($document->getElementsByTagName('span') as $span)
			if (preg_match('/(?:^|\s)(uk-text-(?:success|warning|danger))(?:\s|$)/', $span->getAttribute('class'), $matches))
			{
				$phases[] = '<span class="' . $matches[1] . '">' . e($span->textContent) . '</span>';
				// Una lavorazione avviata è in corso anche prima del primo bancale pronto.
				$hasStartedPhase = $hasStartedPhase || $matches[1] !== 'uk-text-danger';
			}

		if ($phases)
			return implode(' - ', $phases);

		return e($document->getElementsByTagName('body')->item(0)?->textContent ?? '');
	}
}
