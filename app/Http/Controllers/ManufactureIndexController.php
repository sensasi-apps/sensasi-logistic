<?php

namespace App\Http\Controllers;

class ManufactureIndexController extends Controller
{
    public function __invoke()
    {
        $manufactureDatatableAjaxUrl = route('api.datatable', [
            'model_name' => 'ProductManufacture',
            'params_json' => urlencode(json_encode([
                'withs' => [
                    'materialOut.details.materialInDetail' => [
                        'materialIn',
                        'material'
                    ],
                    'productIn.details' => [
                        'outDetails',
                        'product',
                        'stock'
                    ]
                ]
            ]))
        ]);

        $materialManufactureDatatableAjaxUrl = route('api.datatable', [
            'model_name' => 'MaterialManufacture',
            'params_json' => urlencode(json_encode([
                'withs' => [
                    'materialOut.details.materialInDetail' => [
                        'material',
                        'materialIn',
                        'stock'
                    ],
                    'materialIn.details' => [
                        'outDetails',
                        'material',
                        'stock'
                    ],
                ]
            ]))
        ]);

        return view('pages.manufacture.index', compact('manufactureDatatableAjaxUrl', 'materialManufactureDatatableAjaxUrl'));
    }
}
