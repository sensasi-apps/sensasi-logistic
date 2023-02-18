<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaterialController extends Controller
{
    private function validateInput(Request $request, int $materialId = null): array
    {
        $name = $request->name;
        $brand = $request->brand;

        return $request->validate([
            'code' => "nullable|unique:mysql.materials,code,{$materialId}",
            'name' => ['required', Rule::unique('materials')->where(function ($query) use ($name, $brand) {
                return $query
                    ->where('name', $name)
                    ->where('brand', $brand);
            })->ignore($materialId)],
            'brand' => 'nullable',
            'low_qty' => 'numeric',
            'unit' => 'required',
            'tags' => 'nullable|array'
        ]);
    }

    private function getMaterialDatatableApiUrl(): string
    {
        $materialApiParamsJson = json_encode([
            'append' => [
                'has_children'
            ]
        ]);

        return route('api.datatable', ['model_name' => 'Material', 'params_json' => urlencode($materialApiParamsJson)]);
    }

    private function getMaterialInDatatableApiUrl(): string
    {
        $materialInApiParamsJson = json_encode([
            'with' => [
                'details' => [
                    'material:id,name,brand,unit',
                    'outDetails',
                    'stock'
                ],
                'outDetails:material_out_details.id'
            ], 'append' => [
                'has_out_details',
                'details' => 'out_total',
            ]
        ]);

        return route('api.datatable', ['model_name' => 'MaterialIn', 'params_json' => urlencode($materialInApiParamsJson)]);
    }

    private function getMaterialOutDatatableApiUrl(): string
    {
        $materialInApiParamsJson = json_encode([
            'with' => [
                'details' => [
                    'material:id,name,brand,unit',
                    'outDetails',
                    'stock'
                ],
                'outDetails:material_out_details.id'
            ], 'append' => [
                'has_out_details',
                'details' => 'out_total',
            ]
        ]);

        return route('api.datatable', ['model_name' => 'MaterialIn', 'params_json' => urlencode($materialInApiParamsJson)]);
    }

    public function index(): View
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

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $materialFromInput = $this->validateInput($request);

        $material = Material::create($materialFromInput);

        return Helper::getSuccessCrudResponse('added', __('material'), $material->id_for_human);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Material $material): RedirectResponse|JsonResponse
    {
        $materialFromInput = $this->validateInput($request, $material->id);

        $material->update($materialFromInput);

        return Helper::getSuccessCrudResponse('updated', __('material'), $material->id_for_human);
    }

    public function destroy(Material $material): RedirectResponse|JsonResponse
    {
        if ($material->inDetails()->count() > 0) {
            throw new \Exception('Material has in details');
        }

        $material->delete();

        return Helper::getSuccessCrudResponse('deleted', __('material'), $material->id_for_human);
    }
}
