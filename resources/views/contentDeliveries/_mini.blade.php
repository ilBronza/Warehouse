inseriti {{ $contentDelivery->quantity }} su {{ $contentDelivery->getQuantityRequired() }} programmati

- <a href="{{ $contentDelivery->getEditUrl() }}">
	{!! FaIcon::edit() !!} Modifica contenuto spedizione
</a>
