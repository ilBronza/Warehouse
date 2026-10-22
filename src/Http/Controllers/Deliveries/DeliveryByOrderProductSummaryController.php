<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Products\Models\OrderProduct;
use IlBronza\Warehouse\Helpers\Deliveries\OrderProductDeliveriesSummaryHelper;

class DeliveryByOrderProductSummaryController extends DeliveryCRUD
{
    public $allowedMethods = ['summary'];

    public function summary(string $orderProduct)
    {
        $subject = OrderProduct::gpc()::findOrFail($orderProduct);
        $summaryContentDeliveries = OrderProductDeliveriesSummaryHelper::getContentDeliveries($subject);

        return view('warehouse::deliveries._deliveriesSummary', compact('subject', 'summaryContentDeliveries'));
    }
}
