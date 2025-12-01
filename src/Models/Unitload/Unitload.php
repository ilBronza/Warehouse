<?php

namespace IlBronza\Warehouse\Models\Unitload;

use App\Processing;
use App\Providers\Helpers\Processings\ProcessingCreatorHelper;
use Auth;
use Carbon\Carbon;
use IlBronza\AccountManager\Models\User;
use IlBronza\CRUD\Models\BaseModel;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\Clients\Models\Traits\InteractsWithDestinationTrait;
use IlBronza\Products\Models\Finishing;
use IlBronza\Products\Models\OrderProductPhase;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use function dd;

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
			if(! $unitload->isSplitted())
			{
				if (! $processing = $unitload->processing)
				{
					$production = $unitload->production;

					if(! $processing = Processing::where('user_id', Auth::id())->orderByDesc('ended_at')->where('ended_at', '>', Carbon::now()->subMinutes(15))->first())
					{
						$processingParameters = [
							'processing_type' => 'packing',
							'order_product_phase_id' => $production->getKey(),
							'started_at' => Carbon::now(),
							'ended_at' => Carbon::now(),
							'workstation_alias' => $production->getWorkstationId(),
							'user_id' => Auth::id()
						];

						$processing = ProcessingCreatorHelper::createByParameters($processingParameters);
						$processing->terminate();
					}

					$unitloads = $unitload->twins()->whereNull('processing_id')->get();

					foreach($unitloads as $unitload)
					{
						$unitload->processing_id = $processing->getKey();
						$unitload->save();
					}
				}

				$processing->calculateValidPiecesDone();

				if (($production = $unitload->production) && ($production instanceof OrderProductPhase))
					$production->checkCompletion();
			}
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

	public function finishing()
	{
		return $this->belongsTo(Finishing::gpc());
	}

	public function getFinishing() : ?Finishing
	{
		return $this->finishing;
	}

	public function pallettype()
	{
		return $this->belongsTo(Pallettype::gpc());
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
			$this->cacheKey('getTwins'), 5, function ()
		{
//			return static::where('production_id', $this->getProductionId())->count();

			return $this->twins()->count();
		}
		);
	}

	public function twins()
	{
		return $this->hasMany(static::class, 'production_id', 'production_id')
		->where('production_type', $this->production_type);
	}

	public function outherTwins()
	{
		return $this->twins()->where('id', '!=', $this->getKey());
	}

	public function getTwins() : Collection
	{
		return $this->twins;
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

	public function getSplitFormUrl()
	{
		return $this->getKeyedRoute('splitForm');
	}

	public function getStoreSplitUrl()
	{
		return $this->getKeyedRoute('split');
	}

	public function isCompleted() : bool
	{
		return (! ! $this->getQuantity()) && $this->hasBeenPrinted();
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

	public function scopeNotDelivering($query)
	{
		$query->whereNull('content_delivery_id');
	}

	public function scopeDelivering($query)
	{
		$query->whereNotNull('content_delivery_id');
	}

	public function delivery()
    {
        return $this->hasOneThrough(
            Delivery::gpc(),
            ContentDelivery::gpc(),
            'id', # foreign key on intermediary -- categories
            'id', # foreign key on target -- projects
            'content_delivery_id', # local key on this -- properties
            'delivery_id' # local key on intermediary -- categories
        );
    }

	public function getContendDeliveryId() : ?string
	{
		return $this->contend_delivery_id;
	}

	public function getDelivery() : ?Delivery
	{
		return $this->delivery;
	}

	public function hasDelivery() : bool
	{
		return !! $this->getContendDeliveryId();
	}

	public function getVolumeCubicMeters()
	{
		if(! $loadable = $this->getLoadable())
			dd('manca loadable');

		return $loadable->getVolumeCubicMeters();
	}

	public function setQuantityExpected(float $quantityExpected) : self
	{
		$this->quantity_expected = $quantityExpected;

		return $this;
	}

	public function getQuantityExpected() : ?float
	{
		return $this->quantity_expected;
	}

	public function setQuantity(float $quantity) : self
	{
		$this->quantity = $quantity;

		return $this;
	}

	public function getQuantity() : ?float
	{
		return $this->quantity;
	}

	public function setSplitted(bool $splitted = true) : self
	{
		$this->splitted = $splitted;

		return $this;
	}

	public function isSplitted() : bool
	{
		return !! $this->splitted;
	}

	public function setSequence(int $sequence = null) : self
	{
		$this->sequence = $sequence ?? static::where('production_id', $this->getProductionId())->where('production_type', $this->getProductionType())->max('sequence') + 1;

		return $this;
	}

	public function getSequenceString() : string
	{
		return "{$this->getSequence()}/{$this->getBrotherNumbers()}";
	}

	public function resetSequence() : self
	{
		$this->sequence = null;

		return $this;
	}

	public function getName() : string
	{
		if($this->exists)
			return $this->getLoadable()?->getName() . ' - ' . $this->getSequenceString();

		return $this->getLoadable()?->getName() ?? trans('warehouse::unitloads.unitload');
	}

	public function getProductionType() : ?string
	{
		return $this->production_type;
	}

	public function getVolumeMcAttribute() : float
	{
		if($result = ($this->width_mm * $this->length_mm * $this->height_mm))
			return $result;

		return $this->getLoadable()?->getVolumeMc();
	}

	public function getDeliveryDateAttribute() : ? Carbon
	{
		return $this->getDelivery()?->getDateTime();
	}
}