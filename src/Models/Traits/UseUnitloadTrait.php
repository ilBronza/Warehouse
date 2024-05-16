<?php

namespace IlBronza\Warehouse\Models\Traits;

use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;


trait UseUnitloadTrait
{
	public function getFullProductionUnitloads() : Collection
	{
		return $this->productionUnitloads()->with('user.userdata', 'printedBy.userdata')->get();
	}

	public function getProductionUnitloads() : Collection
	{
		return $this->productionUnitloads;
	}

	public function productionUnitloads() : MorphMany
	{
		return $this->morphMany(Unitload::getProjectClassName(), 'production')->orderBy('sequence');
	}
}