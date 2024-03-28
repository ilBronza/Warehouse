<?php

namespace IlBronza\Warehouse\Http\Controllers\Unitloads;

use App\Processing;
use App\Providers\Helpers\Processings\ProcessingCreatorHelper;
use Auth;
use Carbon\Carbon;
use IlBronza\Buttons\Button;
use IlBronza\CRUD\Helpers\ModelManagers\CrudModelStorer;
use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\Products\Models\OrderProductPhase;
use IlBronza\Warehouse\Helpers\UnitloadCreatorHelper;
use IlBronza\Warehouse\Helpers\UnitloadPrinterHelper;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadsCRUDController;
use Illuminate\Http\Request;

class UnitloadsBulkCreateController extends UnitloadsCRUDController
{
    use CRUDCreateStoreTrait;

    public $allowedMethods = ['bulkCreate', 'bulkStore'];

    public function getCreateParametersFile() : ? string
    {
        //IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets\UnitloadBulkCreateStoreFieldsetsParameters
        return config('warehouse.models.unitload.parametersFiles.bulkCreate');
    }

    public function getStoreModelAction()
    {
        return app('warehouse')->route('bulk.store', [$this->orderProductPhase]);
    }

    public function getModelDefaultParameters() : array
    {
        return [
            'orderProductPhase' => $this->orderProductPhase
        ];
    }

    public function addCreateSettings()
    {
        $form = $this->modelFormHelper->form;

        $justCreateButton = Button::create([
            'name' => 'create_unitloads_without_printing',
            'text' => 'warehouse::unitloads.createWithoutPrint'
        ]);

        $justCreateButton->setSubmit();
        $justCreateButton->setDanger();

        $form->addClosureButton($justCreateButton);

        $form->setSubmitButtonText(
            trans('warehouse::unitloads.saveAndPrint')
        );
    }

    public function bulkCreate($orderProductPhase)
    {
        $this->orderProductPhase = OrderProductPhase::getProjectClassName()::with('orderProduct')->findOrFail($orderProductPhase);

        return $this->create();
    }

    public function bulkStore(Request $request, $orderProductPhase)
    {
        $justUnitloadsCreation = $request->input('create_unitloads_without_printing', false);

        $this->orderProductPhase = OrderProductPhase::getProjectClassName()::findOrFail($orderProductPhase);

        $helper = CrudModelStorer::create($this->makeModel(), $this->getStoreParametersClass(), $request);

        $helper->setRequest($request);

        $previousUnitloads = $this->orderProductPhase->getProductionUnitloads();

        $request->validate([
            'unitloads' => 'array|in:' . $previousUnitloads->pluck('id')->implode(",")
        ]);

        $parameters = $helper->getValidatedRequestParameters();

        $processingParameters = [
            'processing_type' => 'packing',
            'order_product_phase_id' => $this->orderProductPhase->getKey(),
            'started_at' => Carbon::now(),
            'ended_at' => Carbon::now(),
            'workstation_alias' => $this->orderProductPhase->getWorkstationId(),
            'user_id' => Auth::id()
        ];

        $processing = ProcessingCreatorHelper::createByParameters($processingParameters);
        $processing->terminate();

        $unitloads = $previousUnitloads->filter(function($item) use($request)
        {
            return in_array($item->getKey(), $request['unitloads'] ?? []);
        });

        if($parameters['packings_quantity'] > 0)
        {
            for($i = 1; $i < $parameters['packings_quantity']; $i++)
            {
                $unitloadParameters = [
                    'production' => $this->orderProductPhase,
                    'loadable' => $this->orderProductPhase->getProduct(),
                    'sequence' => $i + $previousUnitloads->count(),
                    'quantity_capacity' => $parameters['quantity_per_packing'],
                    'quantity_expected' => $parameters['quantity_per_packing'],
                    'quantity' => $justUnitloadsCreation ? null : $parameters['quantity_per_packing'],
                    'user_id' => \Auth::id(),
                    'processing_id' => $processing->getKey(),
                    'destination_id' => $parameters['destination_id'],
                    'pallettype_id' => $parameters['pallettype_id'],
                ];

                $unitloads[] = UnitloadCreatorHelper::createByArray($unitloadParameters);
            }

            $unitloadParameters = [
                'production' => $this->orderProductPhase,
                'loadable' => $this->orderProductPhase->getProduct(),
                'quantity_capacity' => $parameters['quantity_per_packing'],
                'quantity_expected' => $parameters['ordered_quantity'] % $parameters['quantity_per_packing'],
                'user_id' => \Auth::id(),
                'sequence' => $i + $previousUnitloads->count(),
                'processing_id' => $processing->getKey(),
                'destination_id' => $parameters['destination_id'],
                'pallettype_id' => $parameters['pallettype_id'],
            ];

            if(($parameters['valid_pieces_done'])&&(! $justUnitloadsCreation))
                $unitloadParameters['quantity'] = $parameters['valid_pieces_done'] % $parameters['quantity_per_packing'];

            $unitloads[] = UnitloadCreatorHelper::createByArray($unitloadParameters);
        }

        if($justUnitloadsCreation)
            return back();

        return UnitloadPrinterHelper::printUnitloads($unitloads);
    }
}
