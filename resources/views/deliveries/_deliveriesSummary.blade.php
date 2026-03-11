@php use IlBronza\Buttons\Icons\FaIcon; @endphp
@php
	$contentDeliveries = $subject->contentDeliveries ?? collect();
	if (!$contentDeliveries->isEmpty()) {
		$contentDeliveries->load(['delivery', 'unitloads']);
	}
@endphp

@if($contentDeliveries->isEmpty())
	<div class="uk-alert uk-alert-primary">
		@lang('warehouse::deliveries.noContentDeliveries')
	</div>
@else
	<div class="uk-overflow-auto">
		<table class="uk-table uk-table-small uk-table-divider uk-table-striped">
			<thead>
				<tr>
					<th>@lang('warehouse::deliveries.summary.spedizione')</th>
					<th>@lang('warehouse::deliveries.summary.dataSpedizione')</th>
					<th>@lang('warehouse::deliveries.summary.quantitaRichiesta')</th>
					<th>@lang('warehouse::deliveries.summary.quantitaInserita')</th>
					<th>@lang('warehouse::deliveries.summary.parziale')</th>
					<th>@lang('warehouse::deliveries.summary.statoCarico')</th>
					<th>@lang('warehouse::deliveries.summary.avviso')</th>
					<th>@lang('warehouse::deliveries.summary.cliente')</th>
					<th>@lang('warehouse::deliveries.summary.destinazione')</th>
					<th>@lang('warehouse::deliveries.summary.bancali')</th>
					<th>@lang('warehouse::deliveries.summary.peso')</th>
					<th>@lang('warehouse::deliveries.summary.volume')</th>
					<th>@lang('warehouse::deliveries.summary.azioni')</th>
				</tr>
			</thead>
			<tbody>
				@foreach($contentDeliveries->sortBy(fn($cd) => $cd->delivery?->delivery_datetime?->format('Y-m-d H:i') ?? '') as $contentDelivery)
					<tr>
						<td>
							@if($delivery = $contentDelivery->getDelivery())
								<a href="{{ $delivery->getEditUrl() }}">
									{!! FaIcon::edit() !!} {{ $delivery->getName() }}
								</a>
							@else
								—
							@endif
						</td>
						<td>
							{{ $contentDelivery->getDelivery()?->getDateTimeString() ?? '—' }}
						</td>
						<td>{{ number_format($contentDelivery->getQuantityRequired(), 2, ',', '.') }}</td>
						<td>{{ number_format($contentDelivery->getQuantityProduced(), 2, ',', '.') }}</td>
						<td>
							@if($contentDelivery->isPartial())
								<span class="uk-label uk-label-warning">@lang('warehouse::deliveries.summary.si')</span>
							@else
								<span class="uk-label uk-label-success">@lang('warehouse::deliveries.summary.no')</span>
							@endif
						</td>
						<td>
							@if($contentDelivery->isLoaded())
								<span class="uk-label uk-label-success">@lang('warehouse::deliveries.summary.caricato')</span>
							@else
								<span class="uk-label uk-label-warning">@lang('warehouse::deliveries.summary.daCaricare')</span>
							@endif
						</td>
						<td>{{ $contentDelivery->getWarningStatus() }}</td>
						<td>{{ $contentDelivery->getClient()?->getName() ?? '—' }}</td>
						<td>{{ $contentDelivery->getDestination()?->getName() ?? '—' }}</td>
						<td>{{ $contentDelivery->getUnitloads()->count() }}</td>
						<td>{{ number_format($contentDelivery->getWeightKg(), 2, ',', '.') }} kg</td>
						<td>{{ number_format($contentDelivery->getVolumeMc(), 2, ',', '.') }} m³</td>
						<td>
							<a href="{{ $contentDelivery->getEditUrl() }}" class="uk-button uk-button-primary uk-button-small">
								{!! FaIcon::edit() !!}
							</a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endif
