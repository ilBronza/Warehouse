@extends('app')

@section('content')

	<div class="uk-card uk-card-default uk-card-body uk-margin">
		<div class="uk-card-header">
			<h3 class="uk-card-title">@lang('warehouse::deliveries.deliveriesFor', ['model' => $model->getName()])</h3>
		</div>
		<div class="uk-card-body">

			cancellare qua se non serve più
			<div class="uk-grid-small" uk-grid>
				@if($deliveries = $model->getDeliveries())
					@foreach($deliveries as $delivery)
						<div class="uk-width-small uktext-center">
							{{ $delivery->getName() }} <br/>

							<pre>
								{{ $delivery }}
							</pre>
						</div>
					@endforeach
				@endif
			</div>

			fine cancellare qua se non serve più

			<form id="deliveryform" class="uk-form-stacked uk-margin" method="post" action="{{ app('warehouse')->route('unitloads.associateToDeliveryTable') }}">
				@csrf

				@include('warehouse::deliveries._deliveringModel', ['children' => $model->getDeliveringChildren()])

				<button id="associatedelivery" class="uk-button uk-button-primary uk-button-small">
					{!! FaIcon::save() !!} @lang('warehouse::deliveries.associateDelivery')
				</button>

			</form>
		</div>
	</div>

@endsection