@php use IlBronza\Buttons\Icons\FaIcon; @endphp
@foreach($children as $child)
	<div class="uk-card uk-card-small uk-card-default uk-card-body uk-margin">
		<div class="uk-card-header">
			<h4 class="uk-card-title">
				@if($order = $child->getOrder())
					<a href="{{ $order->getEditUrl() }}">{!! FaIcon::edit() !!} Modifica
						ordine {{ $order->getName() }}</a> -
				@endif
				<a href="{{ $child->getEditUrl() }}">{!! FaIcon::edit() !!} Modifica
					componente {{ $child->getName() }}</a>
			</h4>
		</div>
			<div class="uk-card-body">
				@include('warehouse::deliveries._deliveringModelHeader', ['child' => $child])
			</div>
		<div class="uk-card-footer">

			@if(count($undelivering = $child->getProductionUnitloads()->where('content_delivery_id', null)))

				<div class="uk-card uk-card-small uk-card-default uk-margin-bottom content-delivery undelivering">
					<div class="uk-card-header">
						Senza spedizione

						<div class="uk-float-right">

							@include('warehouse::contentDeliveries.__selectAllButtons')

						</div>
					</div>
					<div class="uk-card-body">
						<div uk-grid>
							@foreach($undelivering->sortBy('sequence') as $unitload)
								@include('warehouse::unitloads._mini')
							@endforeach
						</div>
					</div>
				</div>

			@endif

			@if($deliveries = $child->getDeliveries())
				@foreach($deliveries as $delivery)
					@include('warehouse::deliveries._mini')
				@endforeach
			@endif
		</div>

		@if(count($children = $child->getDeliveringChildren()))
			<div class="uk-card-footer">
				@include('warehouse::deliveries._deliveringModels')
			</div>
		@endif
	</div>
@endforeach


<script>
    jQuery('document').ready(function ()
    {
        $('.ib-select-all-unitloads').click(function ()
        {
            $(this).closest('.content-delivery').find('input.unitload').each(function ()
            {
                $(this).prop('checked', true);
            });
        });

        $('.ib-unselect-all-unitloads').click(function ()
        {
            $(this).closest('.content-delivery').find('input.unitload').each(function ()
            {
                $(this).prop('checked', false);
            });
        });
    });
</script>