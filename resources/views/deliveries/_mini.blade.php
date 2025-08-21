@if($contentDelivery = $delivery->pivot)
	@include('warehouse::contentDeliveries._teaser')
@else
<div class="uk-alert uk-alert-danger">
	Qualcosa è andato storto con la spedizione {{ $delivery->getName() }}
</div>
@endif
