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
use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\UnitloadCreatorHelper;
use IlBronza\Warehouse\Helpers\UnitloadPrinterHelper;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadsCRUDController;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UnitloadsBulkCreateController extends UnitloadsCRUDController
{
    use CRUDCreateStoreTrait;

    public $allowedMethods = ['bulkCreate', 'bulkStore'];

    public function getCreateParametersFile() : ? string
    {
        //piecesToPack
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

        $form->setHasSubmitButton(false);

        $form->setSubmitButtontext("Crea nuovi bindelli");
        $form->setCancelButton(false);
    }

    public function bulkCreate($orderProductPhase)
    {
        $this->orderProductPhase = OrderProductPhase::getProjectClassName()::with('orderProduct')->findOrFail($orderProductPhase);

        return $this->create();
    }

    public function validateUnitloads()
    {
        $previousUnitloads = $this->orderProductPhase->getProductionUnitloads();

        $this->request->validate([
            'unitloads' => 'array|in:' . $previousUnitloads->pluck('id')->implode(",")
        ]);
    }

    public function getSelectedUnitloads() : Collection
    {
        return $this->orderProductPhase
                ->productionUnitloads()
                ->whereIn('id', $this->request->unitloads)
                ->get();
    }

    public function bulkDelete()
    {
        $selectedUnitloads = $this->getSelectedUnitloads();

        foreach($selectedUnitloads as $selectedUnitload)
            $selectedUnitload->delete();

        return back();
    }

    public function bulkReset()
    {
        $selectedUnitloads = $this->getSelectedUnitloads();

        foreach($selectedUnitloads as $selectedUnitload)
            UnitloadPrinterHelper::resetUnitloadPrintedAt($selectedUnitload);

        return back();
    }

    public function bulkPrint()
    {
        $selectedUnitloads = $this->getSelectedUnitloads();

        return UnitloadPrinterHelper::printUnitloads($selectedUnitloads);
    }

    public function isDoubleCall() : bool
    {
        $currentRandCheck = $this->request->input('unitloads_rand_check');

        if((session()->get('unitloads_rand_check') == $currentRandCheck))
        {
            Ukn::e(trans('warehouse::unitloads.unitloadsHaveBeenCreatedReprintThemIfYouNeed'));

            return true;
        }

        session()->put('unitloads_rand_check', $currentRandCheck);

        return false;
    }

    private function managePelletIdStoring(array $params)
    {
        if(! isset($params['save_pallettype_id_on']))
            return ;

        if($params['save_pallettype_id_on'] == 'nothing')
            return ;

        if($params['save_pallettype_id_on'] == 'product')
            return $this->orderProductPhase->getProduct()->update(['pallettype_id' => $params['pallettype_id']]);

        if($params['save_pallettype_id_on'] == 'client')
            return $this->orderProductPhase->getOrder()->getClient()->update(['pallettype_id' => $params['pallettype_id']]);

        return null;
    }

    public function bulkStore(Request $request, $orderProductPhase)
    {
        $this->request = $request;
        $this->orderProductPhase = OrderProductPhase::getProjectClassName()::findOrFail($orderProductPhase);

        $this->validateUnitloads();

        if($request->input('delete', false))
            return $this->bulkDelete();

        if($request->input('reset', false))
            return $this->bulkReset();

        if($request->input('print', false))
            return $this->bulkPrint();

        if($this->isDoubleCall())
            return back();

        $request->validate([
            'packings_quantity' => 'min:1'
        ]);

        $totalQuantity = $request->input('valid_pieces_done') ?? $request->input('ordered_quantity');

        $helper = CrudModelStorer::create($this->makeModel(), $this->getStoreParametersClass(), $request);

        $helper->setRequest($request);

        $previousUnitloads = $this->orderProductPhase->getProductionUnitloads();

        $parameters = $helper->getValidatedRequestParameters();

        $this->managePelletIdStoring($parameters);

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

        for($i = 1; $i < $parameters['packings_quantity']; $i++)
        {
            $remaining = $totalQuantity - ($parameters['quantity_per_packing'] * count($previousUnitloads));

            $quantity = $remaining > $parameters['quantity_per_packing']? $parameters['quantity_per_packing'] : $remaining;

            $unitloadParameters = [
                'production' => $this->orderProductPhase,
                'loadable' => $this->orderProductPhase->getProduct(),
                'sequence' => $i + $previousUnitloads->count(),
                'quantity_capacity' => $parameters['quantity_per_packing'],
                'quantity_expected' => $parameters['quantity_per_packing'],
                'quantity' => $quantity,
                'user_id' => \Auth::id(),
                'processing_id' => $processing->getKey(),
                'destination_id' => $parameters['destination_id'],
                'pallettype_id' => $parameters['pallettype_id'],
            ];


            // if(\Auth::id()==1)
            //     dd($unitloadParameters);

            UnitloadCreatorHelper::createByArray($unitloadParameters);
        }

        $unitloadParameters = [
            'production' => $this->orderProductPhase,
            'loadable' => $this->orderProductPhase->getProduct(),
            'quantity_capacity' => $parameters['quantity_per_packing'],
            'quantity_expected' => $parameters['ordered_quantity'] - $this->orderProductPhase->productionUnitloads()->sum('quantity'),
            'user_id' => \Auth::id(),
            'sequence' => $i + $previousUnitloads->count(),
            'processing_id' => $processing->getKey(),
            'destination_id' => $parameters['destination_id'],
            'pallettype_id' => $parameters['pallettype_id'],
        ];

        if(($totalQuantity))
            $unitloadParameters['quantity'] = $totalQuantity - ($parameters['quantity_per_packing'] * (count($previousUnitloads) + $parameters['packings_quantity'] - 1));

        UnitloadCreatorHelper::createByArray($unitloadParameters);

        return back();
    }
}
