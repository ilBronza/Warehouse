<?php

namespace IlBronza\Warehouse\Console\Commands;

use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryFullyDeliveredHelper;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Throwable;

class CheckFullyDeliveredContentDeliveriesCommand extends Command
{
	protected $signature = 'warehouse:content-deliveries:check-fully-delivered
		{--dry-run : Mostra le distinte incoerenti senza modificarle}
		{--chunk=500 : Numero di distinte lette per volta}';

	protected $description = 'Controlla e riallinea il flag fully_delivered delle distinte di consegna';

	protected int $checkedContents = 0;
	protected int $inconsistentContentDeliveries = 0;
	protected int $updatedContentDeliveries = 0;
	protected int $errors = 0;

	/** @var array<string, true> */
	protected array $checkedContentKeys = [];

	public function handle() : int
	{
		$this->resetSummary();
		$chunkSize = $this->getChunkSize();
		$dryRun = (bool) $this->option('dry-run');
		$contentDeliveryClass = ContentDelivery::gpc();

		$contentDeliveryClass::query()
			->whereNull('deleted_at')
			->with('content')
			->orderBy('id')
			->chunkById(
				$chunkSize,
				fn (Collection $contentDeliveries) => $this->checkChunk(
					$contentDeliveries,
					$dryRun
				)
			);

		$this->renderSummary($dryRun);

		return $this->errors === 0 ? self::SUCCESS : self::FAILURE;
	}

	protected function checkChunk(Collection $contentDeliveries, bool $dryRun) : void
	{
		foreach ($contentDeliveries as $contentDelivery)
		{
			$contentKey = $this->contentKey($contentDelivery);

			if (isset($this->checkedContentKeys[$contentKey]))
				continue;

			$this->checkedContentKeys[$contentKey] = true;
			$this->checkedContents++;

			try
			{
				$inconsistentContentDeliveries = ContentDeliveryFullyDeliveredHelper::check(
					$contentDelivery,
					! $dryRun
				);

				$this->inconsistentContentDeliveries += $inconsistentContentDeliveries->count();

				if (! $dryRun)
					$this->updatedContentDeliveries += $inconsistentContentDeliveries->count();
			}
			catch (Throwable $exception)
			{
				$this->errors++;
				$this->error(sprintf(
					'Distinta [%s]: %s',
					$contentDelivery->getKey(),
					$exception->getMessage()
				));
			}
		}
	}

	protected function contentKey(ContentDelivery $contentDelivery) : string
	{
		return $contentDelivery->content_type . ':' . $contentDelivery->content_id;
	}

	protected function getChunkSize() : int
	{
		$chunkSize = (int) $this->option('chunk');

		if ($chunkSize < 1)
			throw new InvalidArgumentException('L\'opzione --chunk deve essere maggiore di zero.');

		return $chunkSize;
	}

	protected function resetSummary() : void
	{
		$this->checkedContents = 0;
		$this->inconsistentContentDeliveries = 0;
		$this->updatedContentDeliveries = 0;
		$this->errors = 0;
		$this->checkedContentKeys = [];
	}

	protected function renderSummary(bool $dryRun) : void
	{
		$this->newLine();
		$this->info($dryRun ? 'Riepilogo simulazione' : 'Riepilogo riallineamento');
		$this->line('Contenuti controllati: ' . $this->checkedContents);
		$this->line('Distinte incoerenti: ' . $this->inconsistentContentDeliveries);

		if (! $dryRun)
			$this->line('Distinte aggiornate: ' . $this->updatedContentDeliveries);

		$this->line('Errori: ' . $this->errors);
	}
}
