<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use Carbon\Carbon;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;

class UnitloadBulkCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        $orderProductPhase = $this->getModel()->orderProductPhase;
        $orderProduct = $orderProductPhase->getOrderProduct();
        $product = $orderProductPhase->getProduct();
        $order = $orderProductPhase->getOrder();
        $client = $orderProductPhase->getClient();

        $palletArray = Pallettype::all()->pluck('name', 'id')->toArray();

        return [
            'order' => [
                'translationPrefix' => 'warehouse::fields',
                'fields' => [
                    'client' => [
                        'type' => 'text',
                        'value' => $client?->getName(),
                        'rules' => 'string|nullable|max:255',
                        'disabled' => true
                    ],
                    'order' => [
                        'type' => 'text',
                        'value' => $order?->getName(),
                        'rules' => 'string|nullable|max:255',
                        'disabled' => true
                    ],
                    'clientOrder' => [
                        'type' => 'text',
                        'value' => $order?->getOrderClientDescription(),
                        'rules' => 'string|nullable|max:255',
                        'disabled' => true
                    ],
                    'product' => [
                        'type' => 'text',
                        'value' => $product?->getName(),
                        'rules' => 'string|nullable|max:255',
                        'disabled' => true
                    ],
                    'productDescription' => [
                        'type' => 'text',
                        'value' => $product?->getShortDescription(),
                        'rules' => 'string|nullable|max:255',
                        'disabled' => true
                    ],
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
            'destination' => [
                'translationPrefix' => 'warehouse::fields',
                'fields' => [
                    'destination_id' => [
                        'type' => 'select',
                        'multiple' => false,
                        'rules' => 'string|nullable|exists:clients__destinations,id',
                        'list' => $order?->getSelectPossibleDestinationIdValues(),
                        'value' => $order->getDestination()?->getKey(),
                        'relation' => 'destination'
                    ],
                    'client_date' => [
                        'type' => 'text',
                        'value' => $order?->getHumanSeparatedDueDate(),
                        'rules' => 'string|nullable|max:64'
                    ],
                    'production_date' => [
                        'type' => 'datetime',
                        'value' => Carbon::now()->format('Y-m-d\TH:i'),
                        'rules' => 'date|nullable'
                    ],

                ],
                'width' => ["1-3@l", '1-2@m']
            ],
            'details' => [
                'translationPrefix' => 'warehouse::fields',
                'fields' => [
                    'fsc_certificate' => [
                        'type' => 'text',
                        'value' => $order?->getFSCDescriptionString(),
                        'rules' => 'string|nullable|max:255'
                    ],
                    'notes' => [
                        'type' => 'textarea',
                        'rules' => 'string|nullable|max:1024'
                    ],
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
            'createdunitloads' => [
                'translationPrefix' => 'warehouse::fields',
                'fields' => [],
                'view' => [
                    'name' => 'warehouse::unitloads.previous'
                ],
                'width' => ['1-1']
            ],

            'newUnitloads' => [
                'translationPrefix' => 'warehouse::fields',
                'fields' => [
                    'pallettype_id' => [
                        'type' => 'select',
                        'value' => $orderProduct?->getPallettypeItem()?->getKey(),
                        'opener' => [
                            'event' => 'change',
                            'targetName' => 'save_pallettype_id_on',
                            'required' => true
                        ],
                        'list' => $palletArray,
                        'rules' => 'string|required|in:' . implode(",", array_keys($palletArray))
                    ],
                    'valid_pieces_done' => [
                        'type' => 'number',
                        'label' => trans('warehouse::unitloads.piecesToPack'),
                        'rules' => 'integer|nullable|min:1|max:65535'
                    ],
                    'ordered_quantity' => [
                        'type' => 'number',
                        'value' => $orderProduct?->getOrderedQuantity(),
                        'rules' => 'integer|required|min:1|max:65535'
                    ],
                    'quantity_per_packing' => [
                        'type' => 'number',
                        'value' => $product?->getQuantityPerPacking(),
                        'rules' => 'integer|required|min:1|max:65535'
                    ],
                    'packings_quantity' => [
                        'type' => 'number',
                        'rules' => 'integer|required|min:0|max:65535'
                    ],
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
        ];
    }
}
