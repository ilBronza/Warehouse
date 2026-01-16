@extends('uikittemplate::app')

@section('content')

	<div class="uk-card uk-card-default uk-card-body uk-margin">
		<div class="uk-card-header">
			<h3 class="uk-card-title">@lang('warehouse::deliveries.deliveriesFor', ['model' => $model->getName()])</h3>
		</div>
		<div class="uk-card-body">

			<form id="deliveryform" class="uk-form-stacked uk-margin" method="post" action="{{ app('warehouse')->route('unitloads.associateToDeliveryTable') }}">
				@csrf

				@include('warehouse::deliveries._deliveringModels')

				<button id="associatedelivery" class="uk-button uk-button-primary uk-button-small">
					{!! FaIcon::save() !!} @lang('warehouse::deliveries.associateDelivery')
				</button>

			</form>
		</div>
	</div>

@endsection