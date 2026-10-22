<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\Deliveries;

use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldFetcher;

class DatatableFieldOrderProductDeliveriesSummary extends DatatableFieldFetcher
{
    public $requiresPlaceholderElement = false;

    public $fetcherData = ['target' => 'currentField'];

    protected function getFetcherUrl(array $fetcherData): ?string
    {
        return app('warehouse')->route('deliveries.orderProducts.summary', [
            'orderProduct' => config('datatables.replace_model_id_string'),
        ]);
    }
}
