<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MaterialIndexController extends Controller
{
    private function getMaterialDatatableApiUrl(): string
    {
        $materialApiParamsJson = json_encode([
            'appends' => [
                'has_children'
            ]
        ]);

        return route('api.datatable', ['model_name' => 'Material', 'params_json' => urlencode($materialApiParamsJson)]);
    }

    private function getMaterialInDatatableApiUrl(): string
    {
        $materialInApiParamsJson = json_encode([
            'withs' => [
                'manufacture',
                'details' => [
                    'material',
                    'outDetails',
                    'stock'
                ],
                'outDetails'
            ], 'appends' => [
                'has_out_details'
            ]
        ]);

        return route('api.datatable', ['model_name' => 'MaterialIn', 'params_json' => urlencode($materialInApiParamsJson)]);
    }

    private function getMaterialOutDatatableApiUrl(): string
    {
        // TODO: optimize columns to be selected
        $materialOutApiParamsJson = json_encode([
            'withs' => [
                'productManufacture',
                'materialManufacture',
                'details.materialInDetail' => [
                    'material',
                    'materialIn',
                    'stock'
                ],
            ]
        ]);

        return route('api.datatable', ['model_name' => 'MaterialOut', 'params_json' => urlencode($materialOutApiParamsJson)]);
    }

    public function __invoke(): View
    {
        $materialInTypes = DB::table('material_ins')->select('type')->distinct()->cursor()->pluck('type');
        $materialOutTypes = DB::table('material_outs')->select('type')->distinct()->cursor()->pluck('type');

        $datatableAjaxUrl = [
            'material' => $this->getMaterialDatatableApiUrl(),
            'material_in' => $this->getMaterialInDatatableApiUrl(),
            'material_out' => $this->getMaterialOutDatatableApiUrl()
        ];

        return view('pages.materials.index', compact('materialInTypes', 'materialOutTypes', 'datatableAjaxUrl'));
    }
}
