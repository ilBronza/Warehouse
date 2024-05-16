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


	// window.getCreatingUnitloadsCount = function ()
	// {
	// 	return parseInt($('#packings-quantity').val());
	// }

	// window.getReprintingUnitloadsCount = function ()
	// {
	// 	return parseInt(document.querySelectorAll('input[type="checkbox"].unitload:checked').length);
	// }

	// window.getReprintingUnitloads = function ()
	// {
	// 	return document.querySelectorAll('input[type="checkbox"].unitload:checked');
	// }

	// window.getPrintingUnitloadsCount = function ()
	// {
	// 	return window.getCreatingUnitloadsCount() + window.getReprintingUnitloadsCount();
	// }

	window.calculatePackingsQuantity = function(alert = true)
	{
		let quantity = ($('#valid-pieces-done').val() > 0) ? $('#valid-pieces-done').val() : $('#ordered-quantity').val();

		let quantityPerPacking = $('#quantity-per-packing').val();

		let packingsNumber = Math.ceil(quantity / quantityPerPacking) - {{ count($unitloads = $modelInstance->orderProductPhase->getFullProductionUnitloads()) }};

		if(packingsNumber < 0)
			packingsNumber = 0;

		// @if(count($unitloads) > 0)
		// window.addWarningNotification('Sono già stati creati {{ count($unitloads) }} colli, puoi ristamparli selezionandoli e premendo stampa');
		// @endif

		$('#packings-quantity').val(packingsNumber);

		if(alert)
			window.addSuccessNotification('Numero colli calcolati: ' + packingsNumber);
	}

	// $('body').on('click', '.close-and-go-to-quantity', function()
	// {
	// 	$(this).closest('.uk-modal.uk-open').hide();
	// 	$('#packings-quantity').focus();
	// });

	$('#valid-pieces-done, #ordered-quantity, #quantity-per-packing').change(function()
	{
		window.calculatePackingsQuantity();
	});

	// if($('form button[name="create_unitloads_without_printing"]').length == 0)
	// 	alert('manca il bottone create_unitloads_without_printing');

	// $('button[name="create_unitloads_without_printing"]').click(function()
	// {
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmitdelete').remove();
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmitreset').remove();

	// 	$('form.ibwarehousebulkcreate').append('<input type="hidden" id="hiddenformsubmit" name="create_unitloads_without_printing" value="1" />');
	// });

	// $('button[name="delete"]').click(function()
	// {
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmit').remove();
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmitreset').remove();

	// 	$('form.ibwarehousebulkcreate').append('<input type="hidden" id="hiddenformsubmitdelete" name="delete" value="1" />');
	// });

	// $('button[name="reset"]').click(function()
	// {
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmit').remove();
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmitdelete').remove();

	// 	$('form.ibwarehousebulkcreate').append('<input type="hidden" id="hiddenformsubmitreset" name="reset" value="1" />');
	// });

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

	// $('form *[name="save"]').click(function()
	// {
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmit').remove();
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmitdelete').remove();
	// 	$('form.ibwarehousebulkcreate #hiddenformsubmitreset').remove();
	// });

	// $('button[name="create_unitloads_without_printing"], button[name="delete"], button[name="reset"]').hover(function()
	// {
  	// 	$(this).closest("form").attr('target', '_self');
  	// });

	$('*[name="print"]').mouseover(function()
	{
  		$(this).closest("form").attr('target', '_blank');
	});

	$('*[name="print"]').mouseout(function()
	{
  		$(this).closest("form").attr('target', '_self');
	});

	// if($('form.ibwarehousebulkcreate').length == 0)
	// {
	// 	alert('missing form with htmlclass ibwarehousebulkcreate');
	// }
	// else $('form.ibwarehousebulkcreate').attr('target', '_blank');

	// $('form.ibwarehousebulkcreate').submit(function(e)
	// {
	// 	e.preventDefault();

	// 	let message = '';

	// 	if(window.getCreatingUnitloadsCount() > 0)
	// 		message += 'Verranno creati <span class="uk-text-bold">' + window.getCreatingUnitloadsCount() + ' nuovi colli</span>';
	// 	else
	// 		message += 'Non verranno creati nuovi colli';

	// 	message += '<br />(come indicato nel campo "Quantità colli DA CREARE")<br />Per modificarlo clicca <a class="close-and-go-to-quantity" href="javascript:void(0)">QUI</a><br /><br />';

	// 	if(window.getReprintingUnitloadsCount() > 0)
	// 		message += 'Verranno stampati ' + window.getPrintingUnitloadsCount() + ' bindelli';

	// 	if(window.getReprintingUnitloadsCount() > 0)
	// 		message += '<br />includendo ' + window.getReprintingUnitloadsCount() + ' bindelli precedentemente creati:';

	// 	for (const [key, value] of Object.entries(window.getReprintingUnitloads()))
	// 	{
	// 		message += '<br /><span class="uk-text-bold">' + $(value).closest('.teaser').find('.label').text() + '</span>';
	// 	}

	// 	message += '.<br />Continuare?';

	// 	var that = this;

	// 	UIkit.modal.confirm(message).then(function() {

	// 		window.addSuccessNotification("Fai refresh della pagina una volta stampato il bindello, cliccando <a href='javascript:void(0)' onclick='location.reload();'>qui</a>", 1, 1000000);

	// 		setTimeout(() => {
	// 			location.reload();
	// 		}, 10000);

	// 		$(that).unbind();

	// 	    that.submit();
	// 	}, function () {
	// 		return false;
	// 	});

	// 	// return false;

	// 	// // if(! confirm(message))
	// 	// 	return false;


	// 	// return true;
	// });

	window.calculatePackingsQuantity(false);
});
</script>

<div uk-grid>

@foreach($unitloads as $unitload)
	@include('warehouse::unitloads._teaser')
@endforeach

</div>
