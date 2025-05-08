<div class="uk-card uk-card-default uk-card-body uk-margin">
	<h3 class="uk-card-title">@lang('warehouse::deliveries.addOrdersToDeliveryTitle')</h3>
	<div class="uk-grid-small" uk-grid>
		@foreach($orders as $order)
			<div class="uk-width-small uktext-center">
				{{ $order->getName() }} <br />
				<span class="uk-text-truncate">
					{{ $order->getClient()?->getName() }}
				</span>

				@foreach($order->getOrderProducts() as $orderProduct)
					<div class="uk-text-truncate">
						{{ $orderProduct->getProduct()->getName() }}
						<span class="uk-text-muted">({{ $orderProduct->getQuantity() }})</span>
					</div>
				@endforeach
			</div>
		@endforeach
	</div>
</div>

<script>

    window.orderIds = {!! $orders->pluck('id')->toJson()  !!};

	jQuery(document).ready(function($)
    {
		$('body').on('click', 'a.addorderstodelivery', function(e)
        {
            e.preventDefault();
            e.stopPropagation();

            var form = document.createElement("form");
            var url = $(e.currentTarget).attr('href');

            form.method = 'POST';
            form.action = url;
            form.style.display = "none";

            for (var key in window.orderIds)
            {
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "ids[]";
                input.value = window.orderIds[key];
                form.appendChild(input);
            }

            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "callertablename";
            input.value = '{{ request()->input('callertablename') }}';
            form.appendChild(input);

            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "_token";

            input.value = window.csrfToken;
            form.appendChild(input);
            document.body.appendChild(form);

            form.submit();
            document.body.removeChild(form);
		});
	});
</script>