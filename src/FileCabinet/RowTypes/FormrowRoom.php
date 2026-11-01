<?php

namespace IlBronza\Warehouse\FileCabinet\RowTypes;

use IlBronza\Clients\Models\Client;
use IlBronza\FileCabinet\Providers\RowTypes\BaseModelReferenceRow;
use IlBronza\Warehouse\Models\Room\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FormrowRoom extends BaseModelReferenceRow
{
    public static function getTranslatedName() : string
    {
        return trans('warehouse::filecabinet.room');
    }

    protected function referenceQuery(bool $historical = false) : Builder
    {
        $query = Room::getProjectClassName()::query();
        if ($historical)
            $query->withoutGlobalScopes();

        $owner = $this->getDossierrow()?->getDossierable();
        if (! $owner instanceof Client || $owner->getKey() === null)
            return $query->whereRaw('1 = 0');

        // A room belongs to a company through its Destination, never its shared Structure.
        return $query->whereHas('destination', function (Builder $destination) use ($owner, $historical) {
            if ($historical)
                $destination->withoutGlobalScopes();

            $destination->where($destination->getModel()->qualifyColumn('client_id'), $owner->getKey());
        });
    }

    protected function referenceLabel(Model $model) : string
    {
        return $model->getName() ?: (string) $model->getKey();
    }
}
