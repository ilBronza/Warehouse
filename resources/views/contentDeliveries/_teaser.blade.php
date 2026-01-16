<div uk-grid class="delivery-summary">
	<div id="content-delivery{{ $contentDelivery->getKey() }}" class="uk-card uk-card-small uk-card-default content-delivery uk-width-expand">
		<div class="uk-card-header">
			@include('warehouse::deliveries._miniHeader')
		</div>
		<div class="uk-card-body">
			@include('warehouse::contentDeliveries._mini', [
				'delivery' => $contentDelivery->delivery
				])
		</div>
		<div class="uk-card-footer">
			<div uk-grid>
				@foreach($contentDelivery->unitloads->sortBy('sequence') as $unitload)
					@include('warehouse::unitloads._mini')
				@endforeach
			</div>
		</div>

	</div>

@if(($other = $contentDelivery->delivery->unitloads->filter(function($item) use($contentDelivery)
{
	return ! $contentDelivery->unitloads->contains($item);
}))&&(count($other)))

	<div class="uk-width-medium other-content-deliveries">
		Altri bancali nella spedizione

		<div>
			<div id="{{ $unitload->getHtmlId() }}" class="uk-width-medium unitload title">
				<div class="@if($unitload->hasBeenPrinted()) uk-background-primary @else uk-background-default @endif uk-padding-remove uk-grid-collapse" uk-grid>
					<span class="uk-h4 uk-text-bold uk-width-expand">
						Tot m³
					</span>
					<span class="uk-width-auto">
						{{ round($other->sum(function($item) { return $item->getVolumeCubicMeters(); }) + $contentDelivery->unitloads->sum(function($item) { return $item->getVolumeCubicMeters(); }), 2) }}
					</span>
				</div>
			</div>

			<ul class="uk-accordion-default" uk-accordion>

				@foreach($other->sortBy(function($item)
				{
					return $item->content_delivery_id . '-' . $item->getPaddedSequenceString(3);
				})->groupBy('content_delivery_id') as $group)
					<li>
						<a class="uk-accordion-title" href>
							{{ $group->first()->getProduction()->getOrder()?->getName() }} -
							{{ $group->first()->getProduction()->getProduct()?->getName() }}
							<span class="uk-float-right">
								{{ round($group->sum(function($item) { return $item->getVolumeCubicMeters(); })) }}m³
							</span>

							<span uk-accordion-icon></span>
						</a>

						<div class="uk-accordion-content">
						@foreach($group as $_other)

							@include('warehouse::unitloads._title', ['unitload' => $_other])

						@endforeach
						</div>

						<progress
								id="js-progressbar{{ $group->first()->content_delivery_id }}"
								class="uk-progress"
								value="{{ count($group->whereNotNull('printed_at')) }}"
								max="{{ count($group) }}">
						</progress>

					</li>

				@endforeach

			</ul>

		</div>
	</div>
@endif


</div>