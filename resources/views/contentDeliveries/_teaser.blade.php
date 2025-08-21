<div id="content-delivery{{ $contentDelivery->getKey() }}" class="uk-card uk-card-small uk-card-default content-delivery">
	<div class="uk-card-header">
		@include('warehouse::deliveries._miniHeader', [
			'delivery' => $contentDelivery->delivery,
			'unitloads' => ($unitloads = $contentDelivery->delivery->getUnitloadsByProduction($contentDelivery->content))
			])

		<div class="uk-float-right">
			@include('warehouse::contentDeliveries.__selectAllButtons', [
				'selector' => '#content-delivery' . $contentDelivery->getKey()
				])
		</div>
	</div>
	<div class="uk-card-body">
		@include('warehouse::contentDeliveries._mini', [
			'delivery' => $contentDelivery->delivery
			])
	</div>
	<div class="uk-card-footer">
		<div uk-grid>
			@foreach($unitloads->sortBy('sequence') as $unitload)
				@include('warehouse::unitloads._mini')
			@endforeach
		</div>
	</div>
</div>
