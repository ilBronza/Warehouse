<a href="{{ $delivery->getEditUrl() }}">
	{!! FaIcon::edit() !!} {{ $delivery->getName() }} - {{ $delivery->getDateTimeString() }}
</a>
- {{ $unitloads->sum('quantity') }}

<br />Volume {{ $delivery->getVolumeMc() }}mc su {{ $delivery->getSuggestedMaximumVolumeMc() }}mc disponibili

