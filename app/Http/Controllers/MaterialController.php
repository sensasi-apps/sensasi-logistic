<?php

namespace App\Http\Controllers;

use Helper;
use App\Models\Material;
use Illuminate\Http\Request;
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
            'appends' => [
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
        $materialInApiParamsJson = json_encode([
            'with' => [
                'manufacture',
                'details.materialInDetail' => [
                    'material',
                    'materialIn'
                ],
            ]
        ]);

        return route('api.datatable', ['model_name' => 'MaterialOut', 'params_json' => urlencode($materialInApiParamsJson)]);
    }

    // TODO: move index to another controller
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
