<script type="text/javascript">
jQuery(document).ready(function($)
{
	window.checkAllUnitloads = function (valore)
	{
		if(valore == "true")
			$('.unitload.teaser input.uk-checkbox.unitload').prop('checked', true);
		else
			$('.unitload.teaser input.uk-checkbox.unitload').prop('checked', false);
	}

	window.checkUnitloadsByInterval = function (from, to)
	{
		if(from > to)
		{
			window.checkAllUnitloads('false');
			window.addErrorNotification('Il campo "Da" non può essere maggiore del campo "A"');
			return false;
		}

		$('.unitload.teaser').each(function()
		{
			let unitloadId = $(this).attr('id');

			var sequence = parseInt(unitloadId.replace('unitload',''));

			if(sequence >= from && sequence <= to)
				$(this).find('input.uk-checkbox.unitload').prop('checked', true);
			else
				$(this).find('input.uk-checkbox.unitload').prop('checked', false);
		});
	}

	window.calculatePackingsQuantity = function(alert = true)
	{
		let quantity = ($('#valid-pieces-done').val() > 0) ? $('#valid-pieces-done').val() : $('#ordered-quantity').val();

		let quantityPerPacking = $('#quantity-per-packing').val();

		let packingsNumber = Math.ceil(quantity / quantityPerPacking) - {{ count($unitloads = $modelInstance->orderProductPhase->getFullProductionUnitloads()) }};

		if(packingsNumber < 0)
			packingsNumber = 0;

		if(alert)
			window.addSuccessNotification('Numero colli calcolati: ' + packingsNumber);
	}

	$('#valid-pieces-done, #ordered-quantity, #quantity-per-packing').change(function()
	{
		window.calculatePackingsQuantity();
	});

	$('input#from, input#to').change(function()
	{
		let from = $('input#from').val();
		if(! from)
		{
			window.checkAllUnitloads('false');

			return false;
		}

		let to = $('input#to').val();
		if(! to)
			{
				window.checkAllUnitloads('false');
				
				return false;
			}

		window.checkUnitloadsByInterval(from, to);
	});

	$('form input[name="all"]').click(function()
	{
		$('input#from').val("");
		$('input#to').val("");

		let value = $('input[name="all"]:checked').val();

		window.checkAllUnitloads(value);
	});

	$('*[name="print"]').mouseover(function()
	{
  		$(this).closest("form").attr('target', '_blank');
	});

	$('*[name="print"]').mouseout(function()
	{
  		$(this).closest("form").attr('target', '_self');
	});

	window.calculatePackingsQuantity(false);
});
</script>

	@foreach($unitloads->groupBy('content_delivery_id') as $key => $group)
		<div class="uk-card uk-card-default uk-card-small uk-margin-bottom">
			<div class="uk-card-header">
				<h3 class="uk-h3">
					<strong>{!! FaIcon::inline('truck') !!} {{ $group->first()->getDelivery()?->getName() ?? 'Senza consegna' }}</strong> - {{ $group->first()->getDelivery()?->getDateTime() ?? 'Data mancante' }}
				</h3>
			</div>
			<div class="uk-card-body">
				<div uk-grid>

					@foreach($group as $unitload)
						@include('warehouse::unitloads._teaser', [
							'showDelivery' => false,
						])
					@endforeach

				</div>

			</div>

		</div>

	@endforeach

