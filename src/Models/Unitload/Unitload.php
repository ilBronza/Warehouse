<?php

namespace IlBronza\Warehouse\Models\Unitload;

use IlBronza\AccountManager\Models\User;
use IlBronza\CRUD\Models\BaseModel;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\Clients\Models\Destination;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Unitload extends BaseModel 
{
    use CRUDUseUuidTrait;
    use PackagedModelsTrait;
    use CRUDParentingTrait;

    public ? string $translationFolderPrefix = 'warehouse';
    static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'unitload';
	static $parentKeyName = 'parent_id';

    public function destination()
    {
        return $this->belongsTo(Destination::getProjectClassName());
    }

    public function getDestination() : ? Destination
    {
        return $this->destination;
    }

    public function production() : MorphTo
    {
        return $this->morphTo();
    }

    public function getProduction() : ? Model
    {
        return $this->production;
    }

    public function loadable() : MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::getProjectClassName());
    }

    public function getUser() : ? User
    {
        return $this->user;
    }

    public function getQuantity() : ? int
    {
        return $this->quantity;
    }

    public function getQuantityCapacity() : ? int
    {
        return $this->quantity_capacity;
    }

    public function pallettype()
    {
        return $this->belongsTo(Pallettype::getProjectClassName());
    }

    public function getPallettype() : ? Pallettype
    {
        return $this->pallettype;
    }

    public function getPallettypeId() : ? string
    {
        return $this->pallettype_id;
    }

    public function getSequence() : ? int
    {
        return $this->sequence;
    }

    public function getProductionId() : string
    {
        return $this->production_id;
    }

    public function getBrotherNumbers() : int
    {
        return static::where('production_id', $this->getProductionId())->count();
    }

    public function getNotes()
    {
        return $this->notes;
    }
}