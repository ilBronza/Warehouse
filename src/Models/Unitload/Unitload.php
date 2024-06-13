<?php

namespace IlBronza\Warehouse\Models\Unitload;

use App\Processing;
use Carbon\Carbon;
use IlBronza\AccountManager\Models\User;
use IlBronza\CRUD\Models\BaseModel;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;

use IlBronza\Clients\Models\Traits\InteractsWithDestinationTrait;
use IlBronza\Products\Models\OrderProductPhase;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Unitload extends BaseModel 
{
    use CRUDUseUuidTrait;
    use PackagedModelsTrait;
    use CRUDParentingTrait;
    use InteractsWithDestinationTrait;

    public ? string $translationFolderPrefix = 'warehouse';
    static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'unitload';
	static $parentKeyName = 'parent_id';

    protected $dates = ['printed_at'];

    static $deletingRelationships = [];

    public function processing()
    {
        return $this->belongsTo(Processing::getProjectClassName());
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

    public function printedBy()
    {
        return $this->belongsTo(User::getProjectClassName(), 'printed_by');
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
        return cache()->remember(
            $this->cacheKey('brotherNumbers'),
            5,
            function()
            {
                return static::where('production_id', $this->getProductionId())->count();
            }
        );
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function getResetPrintingUrl()
    {
        return $this->getKeyedRoute('resetPrintedAt');        
    }

    public function getPrintUrl()
    {
        return $this->getKeyedRoute('print');
    }

    public function isCompleted() : bool
    {
        return (!! $this->getQuantity()) && $this->hasBeenPrinted();
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('quantity')->whereNotNull('printed_at');
    }

    public function getCreatedBy() : ? User
    {
        return $this->getUser();
    }

    public function getPrintedBy() : ? User
    {
        return $this->printedBy;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getPrintedAt() : ? Carbon
    {
        return $this->printed_at;
    }

    public function hasBeenPrinted() : bool
    {
        return !! $this->getPrintedAt();
    }

    protected static function booted()
    {
        static::saved(function($unitload)
        {
            if($processing = $unitload->processing)
                $processing->calculateValidPiecesDone();

            if(($production = $unitload->production)&&($production instanceof OrderProductPhase))
                $production->checkCompletion();
        });

        static::deleted(function($unitload)
        {
            if($processing = $unitload->processing)
                $processing->calculateValidPiecesDone();

            if(($production = $unitload->production)&&($production instanceof OrderProductPhase))
                $production->checkCompletion();
        });
    }

    public function getHtmlId() : string
    {
        return 'unitload' . $this->getSequence();
    }
}