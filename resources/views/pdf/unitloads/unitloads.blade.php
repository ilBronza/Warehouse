<!DOCTYPE html>
<html>
<head>
	<title>Title</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- UIkit CSS -->
	{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.9.4/dist/css/uikit.min.css" /> --}}

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" media="all" />

<style type="text/css">

@page { margin: 10px 20px; }

td
{
	padding
}

table,
h1
{
	margin: 0;
	padding: 0;
}

tr, td
{
	margin: 0px;
	padding: 0px;
}

.bancale td 
{
	line-height: 1.1em;
}

.pagina
{
	page-break-after: auto;
}

h5.small
{
	padding: 5px;
	margin: 5px;
}

h1.small
{
	margin-bottom: 0px;
	padding-bottom: 0px;
}

tr td.cliente
{
	white-space: nowrap;
	outline: hidden;
}

.cliente
{
	font-size: 48px;
	line-height: 1em;
	font-weight: bold;
	
}

.separatore
{
	height: 260px;
	border-bottom-width: 1px;
	border-bottom-style: dashed;
	border-bottom-color: #000;
}

th.intestazione
{
	width: 300px;
	text-align: left;
}

.zone
{
	font-size: 96px;
	margin: 0px;
	line-height: 1em;
}

.colli
{
	font-size: 64px;
}

.printdate
{
	width: 200px;
}

.delivery
{
	font-size: 30px;
	font-weight:bold;
}

@font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-Bold.ttf") }}) format("truetype");
                font-weight: 700;
                font-style: normal;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-BoldItalic.ttf") }}) format("truetype");
                font-weight: 700;
                font-style: italic;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-ExtraBold.ttf") }}) format("truetype");
                font-weight: 800;
                font-style: normal;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-ExtraBoldItalic.ttf") }}) format("truetype");
                font-weight: 800;
                font-style: italic;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-Light.ttf") }}) format("truetype");
                font-weight: 300;
                font-style: normal;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-LightItalic.ttf") }}) format("truetype");
                font-weight: 300;
                font-style: italic;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-Medium.ttf") }}) format("truetype");
                font-weight: 500;
                font-style: normal;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-MediumItalic.ttf") }}) format("truetype");
                font-weight: 500;
                font-style: italic;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-Regular.ttf") }}) format("truetype");
                font-weight: 400;
                font-style: normal;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-SemiBold.ttf") }}) format("truetype");
                font-weight: 600;
                font-style: normal;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-SemiBoldItalic.ttf") }}) format("truetype");
                font-weight: 600;
                font-style: italic;
            }
    
            @font-face {
                font-family: 'Roboto';
                src: url({{ storage_path("fonts/Roboto/Roboto-Italic.ttf") }}) format("truetype");
                font-weight: 400;
                font-style: italic;
            }
    
			body
			{
				font-family: 'Roboto';
				color: #000;
				margin: 15px;
				font-size: 21px;
			}

			.truncate-text {
				overflow: hidden;
				white-space: nowrap;
				text-overflow: ellipsis;
				max-width: 100px;
				font-size: 18px;
			}


</style>
</head>
<body>

	@foreach($unitloads as $unitload)
		@include('warehouse::pdf.unitloads._unitload')
	@endforeach

</body>
</html>