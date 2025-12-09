<div
	id="{{ $unitload->getHtmlId() }}" 
	class="uk-width-medium unitload teaser"
	data-piecesremaining="{{ $unitload->getPiecesSpaceRemaining() }}"
	>
	<div class="uk-card uk-card-small @if($unitload->hasBeenPrinted()) uk-card-primary @else uk-card-default @endif">
		<div class="uk-card-header uk-text-center">
			<span class="label uk-h3">
				{{ $unitload->getSequenceString() }}
			</span>
			<span class="uk-float-right">
				@include('warehouse::unitloads.buttons._printButton')
				@include('warehouse::unitloads.buttons._splitButton')
			</span>
		</div>
		<div class="uk-card-body">
			<span class="uk-h2 uk-text-bold">
			{{ $unitload->getQuantity() }}/{{ $unitload->production?->getOrderProduct()?->getClientQuantity() }}
			</span>
		</div>
		<div class="uk-card-footer">
			<div uk-grid>

				<a class="uk-width-auto" href="javascript:void(0);">
					<label for="unitload{{ $unitload->getKey() }}">
						{{ $unitload->getPallettype()?->getName() }}
					</label>
				</a>

				<div class="uk-width-expand uk-text-right">
					<label for="unitload{{ $unitload->getKey() }}">
						<i class="fa-solid fa-print"></i>
					</label>

					<input class="uk-checkbox unitload" id="unitload{{ $unitload->getKey() }}" type="checkbox" name="unitloads[]" value="{{ $unitload->getKey() }}">
				</div>
			</div>
		</div>
	</div>
</div>
