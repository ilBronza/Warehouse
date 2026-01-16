<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use IlBronza\CRUD\Traits\PackagedHelpersTrait;
use IlBronza\Warehouse\Models\Unitload\Unitload;

class UnitloadMeasuresCalculatorHelper
{
	use PackagedHelpersTrait;

	static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'unitload';
	static $classConfigPrefix = 'measuresHelper';

	static private function getRealNeatHeight(Unitload $unitload, $loadable) : float
	{
		$height = $loadable->getPackingHeight();
		$palletHeight = $unitload->getPallettype()?->getHeightMm() ?? 144;

		$neatHeight = $height - $palletHeight;
		$productRealHeight = $neatHeight * ($unitload->getQuantity() / $unitload->getQuantityCapacity());

		return $productRealHeight + $palletHeight;
	}

	static function calculateVolume(Unitload $unitload)
	{
		if(! $loadable = $unitload->getLoadable())
			return ;

		$width = $loadable->getPackingWidth();
		$length = $loadable->getPackingLength();
		$height = static::getRealNeatHeight($unitload, $loadable);

		if(! ($width && $length && $height))
			return 0;

		return round($width * $length * $height / 1000 / 1000 / 1000, 2);
	}

	static function calculateWeight(Unitload $unitload) : ? float
	{
		if(! $loadable = $unitload->getLoadable())
			return null;

		$pieceWeight = $loadable->getWeightKg();
		$piecesWeightKg = $pieceWeight * $unitload->getQuantity() / 1000;

		$palletWeightKg = $unitload->getPallettype()?->getWeightKg() ?? 25;

		return $piecesWeightKg + $palletWeightKg;
	}

	static function setMeasuresFromQuantity(Unitload $unitload, bool $save = true)
	{
		if(! $loadable = $unitload->getLoadable())
			return null;

		$quantity = $unitload->getQuantity();

		$unitload->width_mm = $loadable->getPackingWidth();
		$unitload->length_mm = $loadable->getPackingLength();
		$unitload->height_mm = static::getRealNeatHeight($unitload, $loadable);

		$unitload->volume_mc = static::gpc()::calculateVolume($unitload);
		$unitload->weight_kg = static::gpc()::calculateWeight($unitload);

		if($save)
			$unitload->save();

		return $unitload;
	}
}