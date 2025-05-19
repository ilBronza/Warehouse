<?php

namespace IlBronza\Warehouse\Models\Unitload;

use App\Processing;
use Carbon\Carbon;
use IlBronza\AccountManager\Models\User;
use IlBronza\Clients\Models\Traits\InteractsWithDestinationTrait;
use IlBronza\CRUD\Models\BaseModel;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\Products\Models\OrderProductPhase;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Unitload extends BaseModel
{
	use CRUDUseUuidTrait;
	use PackagedModelsTrait;
	use CRUDParentingTrait;
	use InteractsWithDestinationTrait;

	static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'unitload';
	static $parentKeyName = 'parent_id';
	static $deletingRelationships = [];
	public ?string $translationFolderPrefix = 'warehouse';
	protected $keyType = 'string';

	protected $casts = [
		'printed_at' => 'datetime'
	];

	protected static function booted()
	{
		static::saved(function ($unitload)
		{
			if ($processing = $unitload->processing)
				$processing->calculateValidPiecesDone();

			if (($production = $unitload->production) && ($production instanceof OrderProductPhase))
				$production->checkCompletion();
		});

		static::deleted(function ($unitload)
		{
			if ($processing = $unitload->processing)
				$processing->calculateValidPiecesDone();

			if (($production = $unitload->production) && ($production instanceof OrderProductPhase))
				$production->checkCompletion();
		});
	}

	public function processing()
	{
		return $this->belongsTo(Processing::getProjectClassName());
	}

	public function production() : MorphTo
	{
		return $this->morphTo();
	}

	public function getProduction() : ?Model
	{
		return $this->production;
	}

	public function loadable() : MorphTo
	{
		return $this->morphTo();
	}

	public function getLoadable()
	{
		return $this->loadable;
	}

	public function user()
	{
		return $this->belongsTo(User::getProjectClassName());
	}

	public function printedBy()
	{
		return $this->belongsTo(User::getProjectClassName(), 'printed_by');
	}

	public function getQuantityCapacity() : ?int
	{
		return $this->quantity_capacity;
	}

	public function pallettype()
	{
		return $this->belongsTo(Pallettype::getProjectClassName());
	}

	public function getPallettype() : ?Pallettype
	{
		return $this->pallettype;
	}

	public function getPallettypeId() : ?string
	{
		return $this->pallettype_id;
	}

	public function getBrotherNumbers() : int
	{
		return cache()->remember(
			$this->cacheKey('brotherNumbers'), 5, function ()
		{
			return static::where('production_id', $this->getProductionId())->count();
		}
		);
	}

	public function getProductionId() : string
	{
		return $this->production_id;
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
		return (! ! $this->getQuantity()) && $this->hasBeenPrinted();
	}

	public function getQuantity() : ?int
	{
		return $this->quantity;
	}

	public function hasBeenPrinted() : bool
	{
		return ! ! $this->getPrintedAt();
	}

	public function getPrintedAt() : ?Carbon
	{
		return $this->printed_at;
	}

	public function scopeCompleted($query)
	{
		return $query->whereNotNull('quantity')->where(function($_query)
			{
    			$_query->where('placeholder', false)->orWhereNull('placeholder');
			});
	}

	public function getCreatedBy() : ?User
	{
		return $this->getUser();
	}

	public function getUser() : ?User
	{
		return $this->user;
	}

	public function getPrintedBy() : ?User
	{
		return $this->printedBy;
	}

	public function getCreatedAt()
	{
		return $this->created_at;
	}

	public function getHtmlId() : string
	{
		return 'unitload' . $this->getSequence();
	}

	public function getSequence() : ?int
	{
		return $this->sequence;
	}

	public function contentDelivery()
	{
		return $this->belongsTo(ContentDelivery::gpc());
	}

	public function getVolumeCubicMeters()
	{
		if(! $loadable = $this->getLoadable())
			dd('manca loadable');

		return $loadable->getVolumeCubicMeters();
	}
}