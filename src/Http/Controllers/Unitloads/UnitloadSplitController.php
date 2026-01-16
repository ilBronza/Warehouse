<?php

namespace IlBronza\Warehouse\Http\Controllers\Unitloads;

use IlBronza\CRUD\Helpers\ModelManagers\CrudModelUpdater;
use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\Warehouse\Helpers\Unitloads\UnitloadSplitterHelper;
use Illuminate\Http\Request;

use function config;
use function redirect;

class UnitloadSplitController extends UnitloadsCRUDController
{
	public $returnBack = true;

	use CRUDEditUpdateTrait;

    public $allowedMethods = [
        'edit',
	    'update'
    ];

	public function getEditParametersFile() : ? string
	{
		return config($this->getBaseConfigName() . ".models.$this->configModelClassName.parametersFiles.split");
	}

	public function edit(string $unitload)
	{
		$unitload = $this->findModel($unitload);

		return $this->_edit($unitload);
	}

	public function getUpdateModelAction()
	{
		return $this->getModel()->getStoreSplitUrl();
	}

	public function update(Request $request, string $unitload)
	{
		$unitload = $this->findModel($unitload);

		$this->setModel($unitload);

		$helper = CrudModelUpdater::create($unitload, new ($this->getEditParametersFile()), $request);

		$helper->setRequest($request);

		$helper->setFieldsetsProvider();

		$validationParamters = $helper->getValidationParameters();

		$parameters = $request->validate($validationParamters);

		UnitloadSplitterHelper::split($unitload, $parameters);

		return redirect()->to(
			$this->getAfterUpdatedRedirectUrl()
		);
	}
}
