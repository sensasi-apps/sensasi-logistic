<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('materials.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedInput = $request->validate([
            'name' => 'required',
            'code' => 'nullable',
            'unit' => 'required',
            'tags' => 'nullable|array'
        ]);
        
        Material::create($validatedInput);

        return redirect(route('materials.index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $update = $request->validate([
            'code' => 'nullable',
            'name' => 'required',
            'unit' => 'required',
            'tags' => 'nullable|array'
        ]);

        $material->update($update);

        return redirect(route('materials.index'))->with('message', [
          'class' => 'success',
          'text' => __(($material->code ?? $material->name) . ' was updated successfully') . '.'
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
        try {
            $material->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }

        return redirect(route('materials.index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);
    }
}