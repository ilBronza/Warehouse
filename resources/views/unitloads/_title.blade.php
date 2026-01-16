<div id="{{ $unitload->getHtmlId() }}" class="uk-width-medium unitload title">
	<div class="@if($unitload->hasBeenPrinted()) uk-background-primary @else uk-background-default @endif uk-padding-remove uk-grid-collapse" uk-grid>
		<span class="uk-h4 uk-text-bold uk-width-expand">
			{{ $unitload->getName() }} - {{ $unitload->getQuantity() }}
		</span>
		<span class="uk-width-auto">
			{{ round($unitload->getVolumeCubicMeters(), 2) }}m³
		</span>
	</div>
</div>
