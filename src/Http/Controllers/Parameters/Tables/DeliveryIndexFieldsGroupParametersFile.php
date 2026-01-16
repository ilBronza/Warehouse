<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DeliveryIndexFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'warehouse::fields',
            'fields' => 
            [
	            'mySelfEdit' => 'links.edit',
	            'mySelfSee' => 'links.see',
                'name' => 'flat',
	            'datetime' => 'dates.date',
	            'vehicle' => 'vehicles::vehicle',
	            // 'assigned_loading_percentage' => 'utilities.milestone',
	            // 'contentDeliveries' => 'warehouse::contentDeliveries.contentList',
	            // 'mySelfClients.contentDeliveries' => 'warehouse::contentDeliveries.clientsList',
	            // 'mySelfDestinations.contentDeliveries' => 'warehouse::contentDeliveries.destinationsList',
	            // 'mySelfUnitloads.contentDeliveries' => 'warehouse::contentDeliveries.unitloadsCountList',
	            // 'mySelfPartial.contentDeliveries' => [
				// 	'type' => 'iterators.each',
		        //     'childParameters' => [
				// 		'type' => 'boolean',
			    //         'property' => 'partial'
		        //     ]
	            // ]
            ]
        ];
	}
}