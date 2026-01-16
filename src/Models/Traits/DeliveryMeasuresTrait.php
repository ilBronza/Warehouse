<?php

namespace IlBronza\Warehouse\Models\Traits;


trait DeliveryMeasuresTrait
{
	public function getWeightKg() : ? float
	{
		return $this->contentDeliveries->sum('weight_kg');
	}

	public function getMaximumWeightKg() : ? float
	{
		return $this->getVehicle()?->getMaximumWeightKg();
	}

	public function getSuggestedMaximumWeightKg() : ? float
	{
		return $this->getVehicle()?->getSuggestedMaximumWeightKg();
	}

	public function getMaximumVolumeMc() : ? float
	{
		return $this->getVehicle()?->getMaximumVolumeMc();
	}

	public function getSuggestedMaximumVolumeMc() : ? float
	{
		return $this->getVehicle()?->getSuggestedMaximumVolumeMc();
	}

	public function getVolumeMc() : float
	{
		return $this->volume_mc;
	}

	public function getVolumeMcAttribute() : float
	{
		return $this->getContentDeliveries()->sum('volume_mc');
	}
	


}