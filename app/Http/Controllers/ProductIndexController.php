<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProductIndexController extends Controller
{
    private function getProductDatatableApiUrl(): string
    {
        $productApiParamsJson = json_encode([
            'appends' => [
                'has_children'
            ]
        ]);

        return route('api.datatable', ['model_name' => 'Product', 'params_json' => urlencode($productApiParamsJson)]);
    }

    private function getProductInDatatableApiUrl(): string
    {
        // TODO: implement check manufacture
        $productInApiParamsJson = json_encode([
            'with' => [
                'details' => [
                    'product',
                    'outDetails',
                    'stock'
                ],
                'outDetails'
            ], 'appends' => [
                'has_out_details'
            ]
        ]);

        return route('api.datatable', ['model_name' => 'ProductIn', 'params_json' => urlencode($productInApiParamsJson)]);
    }

    private function getProductOutDatatableApiUrl(): string
    {
        // TODO: optimize columns to be selected
        $productInApiParamsJson = json_encode([
            'with' => [
                'details.productInDetail' => [
                    'product',
                    'productIn'
                ],
            ]
        ]);

        return route('api.datatable', ['model_name' => 'ProductOut', 'params_json' => urlencode($productInApiParamsJson)]);
    }

    // TODO: move index to another controller
    public function __invoke(): View
    {
        $productInTypes = DB::table('product_ins')->select('type')->distinct()->cursor()->pluck('type');
        $productOutTypes = DB::table('product_outs')->select('type')->distinct()->cursor()->pluck('type');

        $datatableAjaxUrl = [
            'product_list' => $this->getProductDatatableApiUrl(),
            'product_in' => $this->getProductInDatatableApiUrl(),
            'product_out' => $this->getProductOutDatatableApiUrl()
        ];

        return view('pages.products.index', compact('productInTypes', 'productOutTypes', 'datatableAjaxUrl'));
    }
}
