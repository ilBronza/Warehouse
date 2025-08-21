<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Facades\Validator;

use function implode;

class UnitloadSplitterHelper
{
	static function validateParameters(Unitload $unitload, array $parameters)
	{
		$validationParameters = Validator::make(
			$parameters,
			[
				'quantity' => 'required|integer|min:1|max:' . ($unitload->quantity - 1),
				'delivery_id' => 'nullable|exists:' . Delivery::gpc()::make()->getTable() . ',id',
			]
		);

		if($validationParameters->fails()) {
			throw new \InvalidArgumentException('Validation failed: ' . implode(', ', $validationParameters->errors()->all()));
		}
	}

	static function split(Unitload $unitload, array $parameters)
	{
		static::validateParameters($unitload, $parameters);

		$splitted = $unitload->replicate($parameters);

		foreach($parameters as $key => $value)
			$splitted->{$key} = $value;

		$splitted->setQuantityExpected(
			$parameters['quantity']
		);

		$splitted->setSplitted();
		$splitted->setSequence();

		$splitted->save();

		$unitload->setSplitted();

		$unitload->setQuantityExpected(
			$unitload->getQuantityExpected() - $parameters['quantity']
		);

		$unitload->setQuantity(
			$unitload->getQuantity() - $parameters['quantity']
		);

		$unitload->save();
	}
}