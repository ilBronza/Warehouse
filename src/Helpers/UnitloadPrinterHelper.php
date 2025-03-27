<?php

namespace IlBronza\Warehouse\Helpers;

use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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

	public function setUnitloadPrinted(Unitload $unitload)
	{
		$unitload->printed_at = Carbon::now();
		$unitload->printed_by = Auth::id();
		$unitload->save();
	}

	public function setUnitloadsPrinted()
	{
		foreach($this->getUnitloads() as $unitload)
			$this->setUnitloadPrinted($unitload);
	}

	static function resetUnitloadPrintedAt(Unitload $unitload)
	{
		$unitload->printed_at = null;
		$unitload->printed_by = null;
		$unitload->save();
	}

	public function createPDF()
	{
		$this->setUnitloadsPrinted();

		$pdf = PDF::loadView('warehouse::pdf.unitloads.unitloads', ['unitloads' => $this->getUnitloads()]);

		$pdf->setPaper('a4', 'portrait');

		return $pdf->stream();
	}

	static function printUnitloads(array|Collection $unitloads)
	{
		$helper = new static();

		$helper->setUnitloads($unitloads);

		return $helper->createPDF();
	}

	static function printUnitload(Unitload $unitload)
	{
		$helper = new static();

		$helper->addUnitload($unitload);

		return $helper->createPDF();
	}
}