{!! (new \IlBronza\UikitTemplate\Fetcher(['url' => $model->getMapUrl()]))->render() !!}

<script>

setTimeout(() => {
	window.scrollTo({
	    top: document.body.scrollHeight,
	    behavior: 'smooth'
	});

}, 1000);
</script>