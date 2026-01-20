<div class="uk-card uk-card-default uk-card-body uk-margin">
    <h3 class="uk-card-title">@lang('warehouse::deliveries.addOrdersToDeliveryTitle')</h3>
    <div class="uk-grid-small" uk-grid>
        @foreach($filteredGroupedContentDeliveries as $groupedContentDeliveries)
            @foreach($groupedContentDeliveries->contentDeliveries as $contentDelivery)
                <div class="uk-width-small uktext-center">
                    {{ $contentDelivery->getContent()?->getName() }} <br />
                    <span class="uk-text-truncate">
                        {{ $contentDelivery->getContent()?->getClient()?->getName() }}
                    </span>

                    <div class="uk-text-truncate">
                        {{ $contentDelivery->getContent()->getProduct()->getName() }}
                        <span class="uk-text-muted">({{ $contentDelivery->getContent()->getQuantity() }})</span>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>

<script>

    window.groupedContentDeliveriesIds = {!! json_encode($filteredGroupedContentDeliveries->pluck('client_destination_key')) !!};

	jQuery(document).ready(function($)
    {
		$('body').on('click', 'a.addgroupedcontentdeliveriestodelivery', function(e)
        {
            e.preventDefault();
            e.stopPropagation();


            var form = document.createElement("form");
            var url = $(e.currentTarget).attr('href');

            form.method = 'POST';
            form.action = url;
            form.style.display = "none";

            for (var key in window.groupedContentDeliveriesIds)
            {
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "ids[]";
                input.value = window.groupedContentDeliveriesIds[key];
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