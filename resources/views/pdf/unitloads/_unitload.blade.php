<div class="pagina">
	<div class="separatore">
		Creazione bindello: {{ $unitload->created_at->format('d/m/Y') }}<br />
		Operatore: {{ \Auth::user()?->getShortName() }}<br />
		Stampa: {{ Carbon\Carbon::now()->format('d/m/Y H:i:s') }}<br />
	</div>
	<table style="width: 100%;">
		<tr>
			<th class="intestazione">
				COMMESSA:
				<span class="small">{{ $unitload->production?->getOrder()?->getName() }}</span>
			</th>
			<td class="printdate">{{ $unitload->production?->getOrder()->due_date->format('d/m/Y') }}</td>
			<th class="uk-text-left">
				ZONA:
			</th>
		</tr>
		<tr>
			<th class="intestazione">
				SK:
				<span class="small">{{ $unitload->production?->getProduct()?->getName() }}</span>
			</th>
			<td rowspan="4">					
			</td>
			<td rowspan="4" style="text-align:center;">
				<span class="zone">
					{{ $unitload->getDestination()?->getZone() ?? $unitload->getProduction()?->getOrder()?->getDestination()?->getZone() }}
				</span>
			</td>
		</tr>
	</table>
	<table style="width: 100%; margin-top: -25px;">
		<tr>
			<td colspan="2" class="cliente">{!! Str::limit($unitload->production?->getClient()?->getName(), 22, '..'); !!}</td>
		</tr>
		<tr>
			<td colspan="2" class="cliente">
				{{ $unitload->production?->getProduct()?->getShortDescription() }}
			</td>
		</tr>
		<tr>
			<th class="intestazione">DESTINO:</th>
			<td class="truncate-text">{{ $unitload->getDestination()?->getFlatDescriptionString() ?? $unitload->getProduction()?->getOrder()?->getDestination()?->getFlatDescriptionString() }}</td>
		</tr>
		<tr>
			<th class="intestazione">ORDINE CLIENTE:</th>
			<td>{{ $unitload->production?->getOrder()?->getOrderClientDescription() }}</td>
		</tr>
{{-- 		@if($client_date)
		<tr>
			<th class="intestazione">CONSEGNA:</th>
			<td><span class="delivery">{{ $client_date }}</span></td>
		</tr>
		@endif
 --}}

{{--  		<tr>
			<th class="intestazione">OPERATORE:</th>
			<td>{{ $unitload->getUser()?->getShortName() }}</td>
		</tr>
 --}}
{{-- 		<tr>
			<th class="intestazione">CERTIFICATO FSC:</th>
			<td>
			</td>
		</tr>
 --}}		<tr>
			<th class="intestazione"> QTA COLLO / QTA ORD.</th>
			<td class="cliente">{{ $unitload->getQuantity() }}/{{ $unitload->production?->getOrderProduct()->getClientQuantity() }}
			</td>
		</tr>
	</table>
	<table style="width: 100%;">
		<tr>
			<th class="intestazione">COLLI:</th>
			<td class="colli">{{ $unitload->getSequence() }} / {{ $unitload->getBrotherNumbers() }}</td>
		</tr>
		<tr>
			<td colspan="2">
				<div style="margin-bottom: 30px;">{!! $unitload->getNotes() !!}</div>
			</td>
		</tr>
		@if($pallettype = $unitload->getPallettype())
		<tr><th colspan="2"  class="intestazione">BANCALE E FINITURE:</th></tr>
		<tr class="bancale">
			<td colspan="2" style="line-height: 0.7em; font-size: @if(strlen($pallettype->getName()) > 18) 60px; @else 64px; @endif ">{{ $pallettype->getName() }}</td>
		</tr>
		@endif

 		@if(($finishing = $unitload->getFinishing()))
			<tr class="finitura">
				<td colspan="2" style="font-size: @if(strlen($finishing->getName()) > 18) 60px; @else 64px; @endif ">
					{{ $finishing->getName() }}
				</td>
			</tr>
		@endif
 
 	</table>

</div>
