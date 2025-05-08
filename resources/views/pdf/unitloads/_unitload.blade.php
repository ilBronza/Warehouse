<div class="pagina">
	<div class="separatore">
		
	</div>
	<table style="width: 100%;">
		<tr>
			<th class="intestazione">
				COMMESSA:
				<span class="small">{{ $unitload->production?->getOrder()?->getName() }}</span>
			</th>
			<td class="printdate">{{ $unitload->created_at->format('d/m/Y') }}</td>
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
					{{ $unitload->getDestination()?->getZone() }}
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
			<td class="truncate-text">{{ $unitload->getDestination()?->getFlatDescriptionString() }}</td>
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
 --}}		<tr>
			<th class="intestazione">OPERATORE:</th>
			<td>{{ $unitload->getUser()?->getShortName() }}</td>
		</tr>
		<tr>
			<th class="intestazione">CERTIFICATO FSC:</th>
			<td>
				{{-- {{ $fsc }} --}}
			</td>
		</tr>
		<tr>
			<th class="intestazione"> QTA COLLO / QTA ORD.</th>
			<td class="cliente">{{ $unitload->getQuantity() }}/{{ $unitload->production?->getOrderProduct()->getClientQuantity() }}
			</td>
		</tr>
	</table>
	<table style="width: 100%;">
		<tr>
			<th colspan="2" class="intestazione">BANCALE:</th>
		</tr>
		@if($pallettype = $unitload->getPallettype())
		<tr class="bancale">
			<td colspan="2" style="line-height: 0.7em; font-size: @if(strlen($pallettype->getName()) > 18) 60px; @else 64px; @endif ">{{ $pallettype->getName() }}</td>
		</tr>
		@endif
		<tr>
			<th class="intestazione">COLLI:</th>
			<td class="colli">{{ $unitload->getSequence() }} / {{ $unitload->getBrotherNumbers() }}</td>
		</tr>
		<tr>
			<td colspan="2">
				{!! $unitload->getNotes() !!}					
			</td>
		</tr>
	</table>

</div>
