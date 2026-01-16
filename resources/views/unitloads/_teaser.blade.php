<div
	id="{{ $unitload->getHtmlId() }}"
	class="uk-width-medium unitload teaser"
	data-id="{{ $unitload->getKey() }}"
	{{-- data-piecesremaining="{{ $unitload->getPiecesSpaceRemaining() }}" --}}
	>
	<div class="uk-card uk-card-small @if($unitload->hasBeenPrinted()) uk-card-primary @else uk-card-default @endif">
		<div class="uk-card-header uk-text-center">
			<span class="uk-float-left">
				<a class="uk-margin-right" onclick="return confirm('Sei sicuro di voler resettare la stampa di questo bindello?');" title="@lang('warehouse::unitloads.resetUnitloadPrinted')" href="{{ $unitload->getResetPrintingUrl() }}">
					<i class="fa-solid fa-refresh"></i>
				</a>
			</span>
			<span class="label uk-h3">
				{{ $unitload->getSequence() }}/{{ $unitload->getBrotherNumbers() }}
			</span>
			<span class="uk-float-right">
				@include('warehouse::unitloads.buttons._printButton')
				@include('warehouse::unitloads.buttons._editButton')
				{{-- @include('warehouse::unitloads.buttons._splitButton') --}}
				@include('warehouse::unitloads.buttons._deleteButton')
			</span>
		</div>
		<div class="uk-card-body">
			<span class="uk-h2 uk-text-bold">
				{{ $unitload->getQuantity() }}/{{ $unitload->production?->getOrderProduct()?->getClientQuantity() }}
			</span>

			<div>
				{{ $unitload->processing?->getKey() }}
			</div>
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

{{-- 			@if(is_null($showDelivery) || $showDelivery)
			<div class="delivery">
				@if($delivery = $unitload->delivery)
				<div>
					<a href="{{ $delivery->getShowUrl() }}">
						{!! FaIcon::inline('truck') !!} {{ $delivery->getName() }}
					</a>
				</div>
				@else
				<div class="uk-alert-danger" uk-alert>
					<a href class="uk-alert-close" uk-close></a>
					<p>Spedizione mancante</p>
				</div>
				@endif
			</div>
			@endif --}}

			@if($printedAt = $unitload->getPrintedAt())
			<dl class="uk-description-list ib-horizontal-description-list">
				<dt>Stampato da</dt>
				<dd>{{ $unitload->getPrintedBy()?->getShortName() }}</dd>
				<dt>Stampato il</dt>
				<dd>{{ $unitload->getPrintedAt()?->format(trans('dates.date')) }}</dd>
			</dl>
			@else
				<div>Non ancora stampato</div>
			@endif

		</div>
	</div>
</div>
