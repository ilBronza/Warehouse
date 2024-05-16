<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use Carbon\Carbon;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use Illuminate\Support\Str;

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
                    'unitloads_rand_check' => [
                        'type' => 'hidden',
                        'value' => Str::random(16),
                        'rules' => 'string|required'
                    ],
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
                    'save_pallettype_id_on' => [
                        'closed' => true,
                        'label' => 'Salva bancale su',
                        'type' => 'radio',
                        'required' => false,
                        'list' => [
                            'product' => 'Prodotto',
                            'client' => 'Cliente',
                            'nothing' => 'Niente, è solo per questa volta',
                        ],
                        'rules' => 'string|nullable|in:product,client,nothing',
                        'value' => [],
                    ],


                    'valid_pieces_done' => [
                        'type' => 'number',
                        // 'value' => ($quantity = $orderProduct?->getOrderedQuantity() - $orderProduct->getPiecesDone()) > 0 ? $quantity : 0,
                        // 'label' => trans('warehouse::fields.piecesToPack'),
                        'rules' => 'integer|nullable|min:0|max:65535'
                    ],
                    'ordered_quantity' => [
                        'type' => 'number',
                        'value' => $orderProduct?->getOrderedQuantity(),
                        'rules' => 'integer|required|min:1|max:65535'
                    ],
                    'quantity_per_packing' => [
                        'type' => 'number',
                        'value' => $product?->getQuantityPerPacking() ?? $orderProductPhase->productionUnitloads()->first()?->quantity_capacity,
                        'rules' => 'integer|required|min:1|max:65535'
                    ],
                    'packings_quantity' => [
                        'type' => 'number',
                        'rules' => 'integer|required|min:0|max:65535'
                    ],
                    'save' => [
                        'type' => 'button',
                        'htmlClasses' => ['uk-button-large', 'uk-button-primary'],
                        'label' => trans('warehouse::unitloads.createUnitloads'),
                        'fasIcon' => 'plus',
                        'rules' => []
                    ],
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
            'selection' => [
                'translationPrefix' => 'warehouse::fields',
                'fields' => [
                    'from' => [
                        'type' => 'number',
                        'label' => trans('warehouse::fields.select_from'),
                        'rules' => []
                    ],
                    'to' => [
                        'type' => 'number',
                        'label' => trans('warehouse::fields.select_to'),
                        'rules' => []
                    ],
                    'all' => [
                        'type' => 'radio',
                        'default' => false,
                        'label' => trans('warehouse::fields.selection'),
                        'list' => [
                            'true' => 'Seleziona tutti',
                            'false' => 'Deseleziona tutti'
                        ],
                        'rules' => ['nullable']
                    ],
                    'print' => [
                        'type' => 'button',
                        'htmlClasses' => ['uk-button-primary', 'uk-button-large'],
                        'label' => trans('warehouse::unitloads.printSelected'),
                        'fasIcon' => 'file-pdf',
                        'rules' => []
                    ],
                    'reset' => [
                        'type' => 'button',
                        'htmlClasses' => ['uk-button-large'],
                        'label' => trans('warehouse::unitloads.resetSelected'),
                        'fasIcon' => 'refresh',
                        'rules' => []
                    ],
                    'delete' => [
                        'type' => 'button',
                        'htmlClasses' => ['uk-button-danger', 'uk-button-large'],
                        'label' => trans('warehouse::unitloads.deleteSelected'),
                        'fasIcon' => 'trash',
                        'rules' => []
                    ],
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
        ];
    }
}
