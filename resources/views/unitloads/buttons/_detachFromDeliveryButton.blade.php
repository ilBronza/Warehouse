<a class="uk-margin-left" onclick="return confirm('{{ addslashes(__('warehouse::unitloads.detachFromDeliveryConfirm')) }}');" title="@lang('warehouse::unitloads.detachFromDelivery')" href="{{ $unitload->getDetachFromDeliveryUrl() }}">
	<i class="fa-solid fa-link-slash"></i>
</a>
