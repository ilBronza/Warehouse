<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use Illuminate\Http\Request;

use function config;

class DeliveryEditUpdateController extends DeliveryCRUD
{
	use CRUDEditUpdateTrait;

	public $allowedMethods = ['edit', 'update'];

	public function edit(string $delivery)
	{
		$delivery = $this->findModel($delivery);

		return $this->_edit($delivery);
	}

	public function update(Request $request, $delivery)
	{
		$delivery = $this->findModel($delivery);

		return $this->_update($request, $delivery);
	}}
