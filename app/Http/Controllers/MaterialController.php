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
            'unit' => 'required',
            'tags' => 'nullable|array'
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
        $materialOutTypes = DB::connection('mysql')->table('material_out')->select('type')->distinct()->cursor()->pluck('type');
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

        return redirect(route('materials.index'))->with('notifications', [
            [($material->code ?? $material->name) . __(' has been added successfully'), 'success']
        ]);
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

        return redirect(route('materials.index'))->with('notifications', [
            [($material->code ?? $material->name) . __(' has been updated successfully'), 'success']
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Material $material)
    {
        $material->delete();

        return redirect(route('materials.index'))->with('notifications', [
            [($material->code ?? $material->name) . __(' has been deleted successfully'), 'warning']
        ]);
    }
}