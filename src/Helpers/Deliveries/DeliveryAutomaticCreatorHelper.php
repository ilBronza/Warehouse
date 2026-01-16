<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use Carbon\Carbon;
use IlBronza\CRUD\Traits\Helpers\HelperMessageBagTrait;
use IlBronza\CRUD\Traits\PackagedHelpersTrait;
use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DeliveryAutomaticCreatorHelper
{
	use PackagedHelpersTrait;
	use HelperMessageBagTrait;

	static $packageConfigPrefix = 'warehouse';
	static $modelConfigPrefix = 'delivery';
	static $classConfigPrefix = 'automaticCreator';

	static $deliveriesDefaultTimes = [
		'07:00:00' => 'primo mattino',
		'09:00:00' => 'tardo mattino',
		'11:00:00' => 'FIP mattino',
		'14:30:00' => 'primo pomeriggio',
		'15:00:00' => 'Tardo pomeriggio',
		'17:00:00' => 'FIP pomeriggio',
	];

	static $securityLimit = 30;

	public function getSubjectModel() : Model
	{
		return Auth::user();
	}

	static function _getAutomaticName(Carbon $date, string $name = '', Delivery $delivery = null) : string
	{
		$weekMap = [
			0 => 'Domenica',
			1 => 'Lunedi',
			2 => 'Martedi',
			3 => 'Mercoledi',
			4 => 'Giovedi',
			5 => 'Venerdi',
			6 => 'Sabato'
		];

		setlocale(LC_TIME, 'it_IT');

		$pieces = [];

		if (($delivery) && ($delivery->warehouse))
			$pieces[] = $delivery->warehouse;

		$pieces[] = $weekMap[$date->dayOfWeek];
		$pieces[] = $date->translatedFormat('d F');
		$pieces[] = $name;

		return implode(' ', $pieces);
	}

	static function createAutomaticDeliveriesByDate(Carbon $date)
	{
		foreach (static::$deliveriesDefaultTimes as $time => $name)
		{
			$delivery = Delivery::gpc()::make();
			$delivery->name = static::_getAutomaticName($date, $name, $delivery);
			$delivery->datetime = $date->format('Y-m-d') . ' ' . $time;
			$delivery->save();
		}

		Ukn::s(__('warehouse::messages.automaticDeliveriesCreatedSuccessfullyForDate', ['date' => $date->translatedFormat('d F Y')]));
	}

	static function createByDateRange(Carbon $dateStart, Carbon $dateEnd) : bool
	{
		$i = 0;

		while($dateStart <= $dateEnd)
		{
			if(! $dateStart->isWeekend())
				static::createAutomaticDeliveriesByDate($dateStart);

			$dateStart->addDays(1);

			if(($i ++) > static::$securityLimit)
				throw new \Exception(__('warehouse::messages.automaticDeliveryCreationExceededSecurityLimit', ['limit' => static::$securityLimit]));
		}

		return true;
	}
}