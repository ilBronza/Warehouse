<?php

namespace IlBronza\Warehouse\Models\Interfaces;

interface UnitloadProducibleInterface
{
	public function getContentForDelivery() : ? DeliverableInterface;

}