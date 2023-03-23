<?php

namespace App\Http\Controllers;

use Helper;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $materialFromInput = $this->validateInput($request);

        $material = Material::create($materialFromInput);

        return Helper::getSuccessCrudResponse('added', __('material'), $material->id_for_human);
    }

    public function update(Request $request, Material $material): RedirectResponse|JsonResponse
    {
        $materialFromInput = $this->validateInput($request);

        $material->update($materialFromInput);

        return Helper::getSuccessCrudResponse('updated', __('material'), $material->id_for_human);
    }

    public function destroy(Material $material): RedirectResponse|JsonResponse
    {
        if ($material->has_children) {
            throw new \Exception('Material has in details');
        }

        $material->delete();

        return Helper::getSuccessCrudResponse('deleted', __('material'), $material->id_for_human);
    }

    private function validateInput(Request $request): array
    {
        $name = $request->name;
        $brand = $request->brand;

        return $request->validate([
            'code' => "nullable|unique:mysql.materials,code,{$request->id}",
            'name' => [
                'required',
                Rule::unique('materials')->where(function ($query) use ($name, $brand) {
                    return $query
                        ->where('name', $name)
                        ->where('brand', $brand);
                })->ignore($request->id)
            ],
            'brand' => 'nullable',
            'low_qty' => 'nullable|numeric',
            'unit' => 'required',
            'tags' => 'nullable|array'
        ]);
    }
}
