<div id="{{ $unitload->getHtmlId() }}" class="uk-width-medium unitload title">
	<div class="uk-card uk-card-small @if($unitload->hasBeenPrinted()) uk-card-primary @else uk-card-default @endif">
		<div class="uk-card-body">
			<span class="uk-h4 uk-text-bold">
				{{ $unitload->getName() }} - {{ $unitload->getQuantity() }}
			</span>
		</div>
	</div>
</div>
