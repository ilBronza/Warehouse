<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Products\Models\OrderProduct;
use Illuminate\Database\Eloquent\Collection;

class OrderProductDeliveriesSummaryHelper
{
    public static function getContentDeliveries(OrderProduct $subject): Collection
    {
        $subjects = $subject->newCollection([$subject]);

        if ($order = $subject->getOrder()) {
            $pending = [$order->getRootAncestor() ?? $order];
            $orderIds = [];

            while ($current = array_pop($pending)) {
                if (isset($orderIds[$current->getKey()]))
                    continue;

                $orderIds[$current->getKey()] = $current->getKey();
                foreach ($current->getChildren() as $child)
                    $pending[] = $child;
            }

            $subjects = $subject->newQuery()
                ->whereIn('order_id', array_values($orderIds))
                ->where('product_id', $subject->getProductKey())
                ->withLiveClientId()
                ->with('order')
                ->get();
        }

        $subjects->load(['contentDeliveries.delivery', 'contentDeliveries.unitloads']);

        return new Collection($subjects->flatMap(function ($orderProduct) {
            return $orderProduct->getContentDeliveries()->map(function ($contentDelivery) use ($orderProduct) {
                // Reuse the actual content model, including its configured client/destination behavior.
                $contentDelivery->setRelation('content', $orderProduct);

                return $contentDelivery;
            });
        })->unique(fn ($contentDelivery) => $contentDelivery->getKey())->values()->all());
    }
}
