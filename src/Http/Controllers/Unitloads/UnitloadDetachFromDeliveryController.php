<?php

namespace IlBronza\Warehouse\Http\Controllers\Unitloads;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\DeliveryDetacherHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitloadDetachFromDeliveryController extends UnitloadsCRUDController
{
	public $allowedMethods = ['detach', 'detachSelected'];

	public function detachSelected(Request $request)
	{
		$modelClass = $this->getModelClass();
		$model = $modelClass::make();
		$keyName = $model->getKeyName();

		$data = $request->validate([
			'unitloads' => ['required', 'array', 'min:1'],
			'unitloads.*' => ['required', 'string', Rule::exists($modelClass, $keyName)],
		], [
			'unitloads.required' => trans('warehouse::unitloads.selectUnitloadsToDetach'),
		]);

		$detachedCount = $model->getConnection()->transaction(function () use ($modelClass, $keyName, $data)
		{
			$unitloads = $modelClass::whereIn($keyName, $data['unitloads'])
				->with('contentDelivery')
				->lockForUpdate()
				->get();

			$detachedCount = 0;

			foreach ($unitloads as $unitload) {
				if (! $contentDelivery = $unitload->getContentDelivery())
					continue;

				DeliveryDetacherHelper::detachUnitload($unitload, $contentDelivery, false);
				$detachedCount++;
			}

			return $detachedCount;
		});

		$message = $detachedCount
			? trans('warehouse::unitloads.unitloadsDetachedFromDelivery', ['count' => $detachedCount])
			: trans('warehouse::unitloads.selectedUnitloadsNotAssociatedToDelivery');

		if ($request->ajax()) {
			return response()->json([
				'success' => $detachedCount > 0,
				'detached_count' => $detachedCount,
				'message' => $message,
			]);
		}

		if ($detachedCount)
			Ukn::s($message);
		else
			Ukn::e($message);

		return back();
	}

	public function detach(string $unitload)
	{
		$unitload = $this->findModel($unitload);

		$contentDelivery = $unitload->getContentDelivery();

		if (! $contentDelivery) {
			Ukn::e(trans('warehouse::unitloads.unitloadNotAssociatedToDelivery'));

			return back();
		}

		DeliveryDetacherHelper::detachUnitload($unitload, $contentDelivery, false);

		$message = trans('warehouse::unitloads.unitloadDetachedFromDelivery', [
			'unitload' => $unitload->getName(),
		]);

		if (request()->ajax()) {
			return response()->json([
				'success' => true,
				'message' => $message,
			]);
		}

		Ukn::s($message);

		return back();
	}
}
