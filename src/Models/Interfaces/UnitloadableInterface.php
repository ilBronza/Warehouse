<?php

namespace IlBronza\Warehouse\Models\Interfaces;

interface UnitloadableInterface
{
	public function getVolumeCubicMeters() : ? float;
}