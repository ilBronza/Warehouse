<div id="{{ $unitload->getHtmlId() }}" class="uk-width-medium unitload micro">
	<div class="uk-card uk-card-small @if($unitload->hasBeenPrinted()) uk-card-primary @else uk-card-default @endif">
		<div class="uk-card-header uk-text-center">
			<span class="label uk-h3">
				{{ $unitload->delivery->name }}

				{{-- {{ $unitload->getSequenceString() }} --}}
			</span>
		</div>
		<div class="uk-card-body">
			<span class="uk-h2 uk-text-bold">
				{{ $unitload->getQuantity() }}
			</span>
		</div>
	</div>
</div>
