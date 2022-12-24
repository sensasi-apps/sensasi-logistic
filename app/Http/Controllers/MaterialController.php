<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    private function validateInput(Request $request, int $materialId = null)
    {
        return $request->validate([
            'name' => 'required|unique:mysql.materials,name'  . ($materialId ? ",$materialId,id" : null),
            'code' => 'nullable|unique:mysql.materials,code'  . ($materialId ? ",$materialId,id" : null),
            'low_qty' => 'numeric',
            'unit' => 'required',
            'tags' => 'nullable|array'
        ]);
    }

    private function getResponse(string $name, string $type)
    {
        $message = [
            'store' => __('has been added successfully'),
            'update' => __('has been updated successfully'),
            'delete' => __('has been deleted successfully')
        ];

        $color = $type == 'delete' ? 'warning' : 'success';

        if (request()->wantsJson()) {
            return response()->json([
                'notifications' => [[
                    'message' => "$name $message[$type]",
                    'messageHtml' => "<b>$name</b> $message[$type]",
                    'color' => $color
                ]]
            ]);
        }

        return redirect(route('materials.index'))->with('notifications', [
            ["<b>$name</b> $message[$type]", $color]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $materialInTypes = DB::connection('mysql')->table('material_ins')->select('type')->distinct()->cursor()->pluck('type');
        $materialOutTypes = DB::connection('mysql')->table('material_outs')->select('type')->distinct()->cursor()->pluck('type');
        return view('pages.materials.index', compact('materialInTypes', 'materialOutTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $materialFromInput = $this->validateInput($request);

        $material = Material::create($materialFromInput);

        return $this->getResponse($material->code ?? $material->name, 'store');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Material $material)
    {
        $materialFromInput = $this->validateInput($request, $material->id);

        $material->update($materialFromInput);

        return $this->getResponse($material->code ?? $material->name, 'update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Material $material)
    {

        if ($material->inDetails()->count() > 0) {
            return redirect(route('materials.index'))->with('notifications', [
                ['<b>' . ($material->code ?? $material->name) . '</b> ' . __('cannot be deleted. Material(s) has been used'), 'danger']
            ]);
        }

        $material->delete();

        return $this->getResponse($material->code ?? $material->name, 'delete');
    }
}
