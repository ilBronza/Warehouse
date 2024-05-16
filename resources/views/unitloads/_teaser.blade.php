<div id="{{ $unitload->getHtmlId() }}" class="uk-width-medium unitload teaser">
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
				<a target="_blank" title="@lang('warehouse::unitloads.printUnitloadQuantity')" href="{{ $unitload->getPrintUrl() }}">
					<i class="fa-solid fa-print"></i>
				</a>
				<a class="uk-margin-left" title="@lang('warehouse::unitloads.editUnitloadQuantity')" href="{{ $unitload->getEditUrl() }}">
					<i class="fa-solid fa-pen-to-square"></i>
				</a>
				<a class="uk-margin-left" onclick="return confirm('Sei sicuro?');" title="@lang('warehouse::unitloads.deleteUnitloadQuantity')" href="{{ $unitload->getDeleteUrl() }}">
					<i class="fa-solid fa-trash"></i>
				</a>
			</span>
		</div>
		<div class="uk-card-body">
			<span class="uk-h2 uk-text-bold">
			{{ $unitload->getQuantity() }}/{{ $unitload->getProduction()?->getQuantityRequired() }}
			</span>

			<dl class="uk-description-list ib-horizontal-description-list">
				<dt>Creato da</dt>
				<dd>{{ $unitload->getCreatedBy()?->getShortName() }}</dd>
				<dt>Creato il</dt>
				<dd>{{ $unitload->getCreatedAt()?->format(trans('dates.date')) }}</dd>
			</dl>
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

			@if($printedAt = $unitload->getPrintedAt())
			<dl class="uk-description-list ib-horizontal-description-list">
				<dt>Stampato da</dt>
				<dd>{{ $unitload->getPrintedBy()?->getShortName() }}</dd>
				<dt>Stampato il</dt>
				<dd>{{ $unitload->getPrintedAt()?->format(trans('dates.date')) }}</dd>
			@else
			<dl class="uk-description-list">
				<dt>Non ancora stampato</dt>
				<dd></dd>
				<dt></dt>
				<dd></dd>
			@endif
			</dl>

		</div>
	</div>
</div>
