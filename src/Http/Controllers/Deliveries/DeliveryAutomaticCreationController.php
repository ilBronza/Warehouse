<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use Carbon\Carbon;
use IlBronza\Form\Form;
use IlBronza\FormField\FormField;
use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryAutomaticCreatorHelper;
use Illuminate\Http\Request;

use function app;
use function back;

class DeliveryAutomaticCreationController extends DeliveryCRUD
{
	public $allowedMethods = [
		'form',
		'store'
	];

	public function form()
	{
		$form = Form::createFromArray([
			'action' => app('warehouse')->route('deliveries.automaticCreationStore'),
			'method' => 'POST'
		]);

		$form->setTitle(__('warehouse::deliveries.createAutomaticallyByDate'));
		$form->setCard();

		$form->addFormField(
			FormField::createFromArray([
				'label' => trans('warehouse::fields.date_from'),
				'name' => 'dateFrom',
				'type' => 'date',
			])
		)->addFormField(
			FormField::createFromArray([
				'label' => trans('warehouse::fields.date_to'),
				'name' => 'dateTo',
				'type' => 'date',
			])
		);

		return $form->render();
	}

	public function store(Request $request)
	{
		$request->validate([
			'dateFrom' => 'required|date',
			'dateTo' => 'required|date|after_or_equal:dateFrom',
		]);

		$dateStart = Carbon::parseFromLocale($request->input('dateFrom'));
		$dateEnd = Carbon::parseFromLocale($request->input('dateTo'));

		Carbon::setLocale('it');


		if(DeliveryAutomaticCreatorHelper::gpc()::createByDateRange($dateStart, $dateEnd))
			Ukn::s(__('warehouse::messages.deliveriesCreatedByDateRangeSuccessfully'));

		else
			Ukn::e(DeliveryAutomaticCreatorHelper::gpc()::getMessagesBag());

		return redirect()->to(
			app('warehouse')->route('deliveries.active')
		);
	}


}
