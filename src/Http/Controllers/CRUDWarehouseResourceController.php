<?php

namespace IlBronza\Warehouse\Http\Controllers;

use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDDeleteTrait;
use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;
use Illuminate\Http\Request;

abstract class CRUDWarehouseResourceController extends CRUDWarehousePackageController
{
	use CRUDCreateStoreTrait;
	use CRUDDeleteTrait;
	use CRUDEditUpdateTrait;
	use CRUDIndexTrait;
	use CRUDPlainIndexTrait;
	use CRUDRelationshipTrait;
	use CRUDShowTrait;

	public $allowedMethods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
	protected array $indexRelations = [];

	public function getCreateParametersFile() : ?string
	{
		return config("warehouse.models.{$this->configModelClassName}.parametersFiles.create");
	}

	public function getIndexFieldsArray() : array
	{
		$class = config("warehouse.models.{$this->configModelClassName}.fieldsGroupsFiles.index");

		return $class::getTracedFieldsGroup();
	}

	public function getRelatedFieldsArray() : array
	{
		$class = config("warehouse.models.{$this->configModelClassName}.fieldsGroupsFiles.related");

		return $class::getTracedFieldsGroup();
	}

	public function getIndexElements()
	{
		return $this->getModelClass()::with($this->indexRelations)->get();
	}

	public function show(string $id)
	{
		return $this->_show($this->getModelClass()::findOrFail($id));
	}

	public function edit(string $id)
	{
		return $this->_edit($this->getModelClass()::findOrFail($id));
	}

	public function update(Request $request, string $id)
	{
		return $this->_update($request, $this->getModelClass()::findOrFail($id));
	}

	public function destroy(string $id)
	{
		return $this->_destroy($this->getModelClass()::findOrFail($id));
	}
}
