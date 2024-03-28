<?php

namespace IlBronza\Warehouse\Helpers;

use Barryvdh\DomPDF\Facade as PDF;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

class UnitloadPrinterHelper
{
	public Collection $unitloads;

	public function __construct()
	{
		$this->unitloads = collect();
	}

	public function addUnitload(Unitload $unitload)
	{
		$this->unitloads->push($unitload);
	}

	public function setUnitloads(array|Collection $unitloads)
	{
		foreach($unitloads as $unitload)
			$this->addUnitload($unitload);
	}

	public function getUnitloads() : Collection
	{
		return $this->unitloads;
	}

	public function createPDF()
	{
		// $username = Auth::user()->getShortPrivacyName();

		// $this->managePelletIdStoring($orderProduct, $params);

		// if(\Auth::id() == 1)
		// 	return view('pdf.bindello', $params);

		// if($pallettypeId = $params['pallettype_id'])
		// 	Pallettype::lowerQuantity($pallettypeId, $params['colli']);

		// $pdf = PDF::loadView('pdf.bindello', $params);
		$pdf = PDF::loadView('warehouse::pdf.unitloads.unitloads', ['unitloads' => $this->getUnitloads()]);

		$pdf->setPaper('a4', 'portrait');

		return $pdf->stream();
	}

	static function printUnitloads(array|Collection $unitloads)
	{
		$helper = new static();

		$helper->setUnitloads($unitloads);

		return $helper->createPDF();

		$helper->stream();
	}

}