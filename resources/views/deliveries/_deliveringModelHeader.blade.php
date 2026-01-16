<div uk-grid>
	<dl class="uk-width-medium">
		<dt>Produzione richiesta</dt>
		<dd>{{ $child->getQuantityRequired() }}</dd>
	</dl>
	<dl class="uk-width-medium">
		<dt>Pezzi in spedizione</dt>
		<dd>
			@if($deliveries = $child->getDeliveries())
				inseriti {{ $deliveries->sum('pivot.quantity') }} su
				{{ $deliveries->sum('pivot.quantity_required') }} richiesti da
				{{ $deliveries->count() }} spedizioni
			@endif
		</dd>
	</dl>
	<dl class="uk-width-medium">
		<dt>Pezzi prodotti</dt>
		<dd>{{ $child->getQuantityDone() }}
			({{ $child->isCompleted() ? 'Completato' : 'Non completato' }})
		</dd>
	</dl>
</div>
