<?php

namespace IlBronza\Warehouse\FileCabinet\RowTypes;

use IlBronza\FileCabinet\Providers\RowTypes\BaseModelReferenceRow;
use IlBronza\Warehouse\Models\Room\RoomType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormrowRoomType extends BaseModelReferenceRow
{
    public static function getTranslatedName() : string
    {
        return trans('warehouse::filecabinet.roomType');
    }

    protected function referenceQuery(bool $historical = false) : Builder
    {
        // Room types are a shared catalog, independent of a company's rooms.
        $query = RoomType::getProjectClassName()::query();
        if ($historical)
            $query->withoutGlobalScope(SoftDeletingScope::class);

        return $query;
    }

    protected function referenceLabel(Model $model) : string
    {
        return $model->getName() ?: (string) $model->getKey();
    }
}
