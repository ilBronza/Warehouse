<?php

namespace IlBronza\Warehouse\FileCabinet\RowTypes;

use IlBronza\FileCabinet\Providers\RowTypes\MultipleModelReferencesTrait;

class FormrowRooms extends FormrowRoom
{
    use MultipleModelReferencesTrait;

    static $databaseField = 'text';
    public ?bool $multiple = true;

    public static function getTranslatedName() : string
    {
        return trans('warehouse::filecabinet.rooms');
    }
}
