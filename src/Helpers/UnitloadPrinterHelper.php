<?php

namespace IlBronza\Warehouse\Helpers;

use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class UnitloadPrinterHelper
{
	public Collection $unitloads;

	public string $viewName = 'warehouse::pdf.unitloads.unitloads';

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

	public function getView()
	{
		return $this->viewName;
	}

	public function createPdf()
	{
		$this->setUnitloadsPrinted();

		$pdf = PDF::loadView($this->getView(), ['unitloads' => $this->getUnitloads()]);

		$pdf->setPaper('a4', 'portrait');

		return $pdf;
	}

	public function getFilePath() : string
	{
		$orderProduct = $this->getUnitloads()->first()->getProduction()?->getOrderProduct();

		$sequence = $orderProduct->getProduct()->getComponentSequence();

		$orderName = $orderProduct->getOrder()->getName() . $sequence;

		return $orderName . '.pdf';
	}

	public function getTempFilePath() : string
	{
		return '/temp/' . $this->getFilePath();		
	}

	public function createAndGetTempFile() : string
	{
		return storage_path(
			$this->saveTempPdf()
		);
	}

	public function saveTempPdf() : string
	{
		$pdf = $this->createPdf();

		$path = $this->getTempFilePath();

		$pdf->save(
			storage_path($path)
		);

		return $path;
	}

	public function streamPdf()
	{
		$pdf = $this->createPdf();

		return $pdf->stream();
	}

	static function printUnitloads(array|Collection $unitloads)
	{
		$helper = new static();

		$helper->setUnitloads($unitloads);

		return $helper->streamPdf();
	}

	static function printUnitload(Unitload $unitload)
	{
		$helper = new static();

		$helper->addUnitload($unitload);

		return $helper->streamPdf();
	}
}