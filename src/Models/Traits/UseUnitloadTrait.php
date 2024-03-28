<?php

namespace IlBronza\Warehouse\Models\Traits;

use IlBronza\Warehouse\Models\Unitload\Unitload;


trait UseUnitloadTrait
{
	public function getProductionUnitloads()
	{
		return $this->productionUnitloads;
	}

	public function productionUnitloads()
	{
		return $this->morphMany(Unitload::getProjectClassName(), 'production')->orderBy('sequence');
	}
}