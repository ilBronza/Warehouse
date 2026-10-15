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

				<button type="submit" id="associatedelivery" class="uk-button uk-button-primary uk-button-small">
					{!! FaIcon::save() !!} @lang('warehouse::deliveries.associateDelivery')
				</button>

				<button type="submit" id="detachunitloadsfromdelivery" class="uk-button uk-button-danger uk-button-small"
					formaction="{{ app('warehouse')->route('unitloads.detachSelectedFromDelivery') }}"
					data-empty-message="@lang('warehouse::unitloads.selectUnitloadsToDetach')"
					data-confirm="@lang('warehouse::unitloads.detachSelectedFromDeliveryConfirm')"
					onclick="if (!this.form.querySelector('input[name=&quot;unitloads[]&quot;]:checked')) { alert(this.dataset.emptyMessage); return false; } return confirm(this.dataset.confirm);">
					<i class="fa-solid fa-link-slash"></i> @lang('warehouse::unitloads.detachSelectedFromDelivery')
				</button>

			</form>
		</div>
	</div>

@endsection
