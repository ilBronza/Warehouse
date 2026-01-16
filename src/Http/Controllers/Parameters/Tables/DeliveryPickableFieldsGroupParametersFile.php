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

	            'datetime' => [
		            'type' => 'dates.datetime',
		            'order' => [
			            'priority' => 10,
		            ]
	            ],

	            'mySelfTime.datetime' => [
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
	            'contentDeliveries' => 'warehouse::contentDeliveries.contentList',
	            'mySelfClients.contentDeliveries' => 'warehouse::contentDeliveries.clientsList',
	            'mySelfDestinations.contentDeliveries' => 'warehouse::contentDeliveries.destinationsList',
	            'mySelfZone.contentDeliveries' => 'warehouse::contentDeliveries.zonesList',

	            'mySelfOrdersWarnedList' => [
		            'type' => 'function',
		            // 'function' => 'getOrdersString',
		            'function' => 'TODO_DOGODO_GRAVE_getClientsWarned',
		            'width' => '70px'
	            ],

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

	            'mySelfUnitloads.contentDeliveries' => 'warehouse::contentDeliveries.unitloadsCountList',
	            'shipping_status_color' => 'color',
	            'mySelfPartial.contentDeliveries' => [
		            'translatedName' => trans('warehouse::fields.partial'),
		            'type' => 'iterators.each',
		            'childParameters' => [
			            'type' => 'boolean',
			            'property' => 'partial'
		            ]
	            ],
            ]
        ];
	}
}