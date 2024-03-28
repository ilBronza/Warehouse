<script type="text/javascript">
jQuery(document).ready(function($)
{
	window.getCreatingUnitloadsCount = function ()
	{
		return parseInt($('#packings-quantity').val());
	}

	window.getReprintingUnitloadsCount = function ()
	{
		return parseInt(document.querySelectorAll('input[type="checkbox"].unitload:checked').length);
	}

	window.getPrintingUnitloadsCount = function ()
	{
		return window.getCreatingUnitloadsCount() + window.getReprintingUnitloadsCount();
	}

	window.calculatePackingsQuantity = function()
	{
		let quantity = ($('#valid-pieces-done').val() > 0) ? $('#valid-pieces-done').val() : $('#ordered-quantity').val();

		let quantityPerPacking = $('#quantity-per-packing').val();

		let packingsNumber = Math.ceil(quantity / quantityPerPacking) - {{ count($unitloads = $modelInstance->orderProductPhase->getProductionUnitloads()) }};

		if(packingsNumber < 0)
			packingsNumber = 0;

		@if(count($unitloads) > 0)
		window.addDangerNotification('Sono già stati creati {{ count($unitloads) }} colli, puoi ristamparli selezionandoli e premendo stampa');
		@endif

		$('#packings-quantity').val(packingsNumber);

		window.addSuccessNotification('Numero colli calcolati: ' + packingsNumber);
	}

	$('#valid-pieces-done, #ordered-quantity, #quantity-per-packing').change(function()
	{
		window.calculatePackingsQuantity();
	});


	if($('form button[name="create_unitloads_without_printing"]').length == 0)
		alert('manca il bottone create_unitloads_without_printing');

	$('button[name="create_unitloads_without_printing"]').hover(function()
	{
  		$(this).closest("form").attr('target', '_self');
  	}, function()
  	{
  		$(this).closest("form").attr('target', '_blank');
	});

	if($('form.ibwarehousebulkcreate').length == 0)
	{
		alert('missing form with htmlclass ibwarehousebulkcreate');
	}
	else $('form.ibwarehousebulkcreate').attr('target', '_blank');

	$('form.ibwarehousebulkcreate').submit(function(e)
	{
		if(! confirm("Verranno creati " + window.getCreatingUnitloadsCount() + " nuovi colli, e stampati " + window.getPrintingUnitloadsCount() + " includendo la ristampa di " + window.getReprintingUnitloadsCount() + " colli precedenti. Continuare?"))
			return false;

		window.addSuccessNotification("Fai refresh della pagina una volta stampato il bindello, cliccando <a href='javascript:void(0)' onclick='location.reload();'>qui</a>", 1, 1000000);

		setTimeout(() => {
			location.reload();
		}, 10000);

		return true;
	});

	window.calculatePackingsQuantity();
});
</script>

<div uk-grid>

@foreach($unitloads as $unitload)
<div>
	<div class="uk-card uk-card-small @if($unitload->getQuantity() == 0) uk-alert-danger @elseif($unitload->getQuantity() != $unitload->getQuantityCapacity()) uk-card-secondary @else uk-card-primary @endif">
		<div class="uk-card-header uk-text-center">
			<span class="uk-h3">
				{{ $unitload->getSequence() }}/{{ $unitload->getBrotherNumbers() }}
			</span>
			<span class="uk-float-right">
				<a title="@lang('warehouse::unitloads.editUnitloadQuantity')" href="{{ $unitload->getEditUrl() }}">
					<i class="fa-solid fa-pen-to-square"></i>
				</a>
			</span>
		</div>
		<div class="uk-card-body">
			{{ $unitload->getQuantity() }}/{{ $unitload->getQuantityCapacity() }}
		</div>
		<div class="uk-card-footer">
			<a href="javascript:void(0);">
				<label for="unitload{{ $unitload->getKey() }}">
					{{ $unitload->getPallettype()?->getName() }}
				</label>
			</a>

			<input class="uk-checkbox unitload" id="unitload{{ $unitload->getKey() }}" type="checkbox" name="unitloads[]" value="{{ $unitload->getKey() }}">
		</div>
	</div>
</div>
@endforeach

</div>
