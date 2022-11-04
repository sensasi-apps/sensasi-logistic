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
        $data['materials'] = Material::all();

        return view('materials.index', $data);
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
            'unit' => 'required'
        ]);
        
        if ($request->tags) {
            $validatedInput['tags_json'] = json_encode($request->tags);
        }

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
            'tags' => 'nullable'
        ]);

        if ($request->tags) {
            $update['tags'] = json_encode($request->tags);
        }

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