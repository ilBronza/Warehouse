<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

use function trans;

class DeliveryPickableFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'warehouse::fields',
            'fields' => 
            [
	            'mySelfIsFip' => [
		            'type' => 'function',
		            'function' => 'isFipString',
		            'renderAs' => 'boolean',
		            'valueAsRowClass' => true,
	            ],
	            'hypothetical' => [
		            'type' => 'booleanAlarm',
		            'valueAsRowClass' => true
	            ],

	            'delivery_datetime' => [
		            'type' => 'dates.datetime',
		            'order' => [
			            'priority' => 10,
		            ]
	            ],

	            'mySelfTime.delivery_datetime' => [
		            'type' => 'dates.format',
		            'width' => '25px',
		            'format' => 'a',
		            'valueAsRowClass' => true
	            ],

	            'name' => 'warehouse::deliveries.name',
				'mySelfAddToDelivery' => 'warehouse::deliveries.addOrders',

	            'mySelfOrdersCompletion' => [
		            'type' => 'function',
		            'function' => 'getOrdersCompletionString',
		            'width' => '70px'
	            ],
	            'mySelfWeight' => 'warehouse::deliveries.weight',
	            'mySelfVolumne' => 'warehouse::deliveries.volume',

	            'vehicle' => 'vehicles::vehicle',
	            'assigned_loading_percentage' => 'utilities.milestone',
				'groupedClientsContentDeliveries' => 'warehouse::groupedClientsContentDeliveries.contentList',
				'mySelfClients.groupedClientsContentDeliveries' => 'warehouse::groupedClientsContentDeliveries.clientsList',
				'mySelfDestinations.groupedClientsContentDeliveries' => 'warehouse::groupedClientsContentDeliveries.destinationsList',
				'mySelfZone.groupedClientsContentDeliveries' => 'warehouse::groupedClientsContentDeliveries.zonesList',

	            'mySelfOrdersNotesList' => [
		            'visible' => false,
		            'type' => 'function',
		            // 'function' => 'getOrdersString',
		            'function' => 'getClientsShippingNotesString',
		            'width' => '620px'
	            ],

	            'mySelfLoadingStatus' => [
		            'type' => 'function',
		            'function' => 'getLoadingString',
		            'width' => '40px'
	            ],

				'mySelfUnitloads.groupedClientsContentDeliveries' => 'warehouse::groupedClientsContentDeliveries.unitloadsCountList',
	            'shipping_status_color' => 'color',
            ]
        ];
	}
}