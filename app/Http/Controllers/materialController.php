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

        return view('material.index', $data);
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
            'code' => 'required',
            'unit' => 'required',
            'tags_json' => 'nullable',
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
    public function update(Request $request, $id)
    {
        $update = $request->validate([
            'code' => 'required',
            'name' => 'required',
            'unit' => 'required',
            'tags_json' => 'nullable'
        ]);

        $materi = Material::find($request->id);
        if ($request->tags_json) {
            $update['tags_json'] = $materi->tags_json.",".$update['tags_json'];
        } else{
            $update['tags_json'] = $materi->tags_json;
        }
        $materi->update($update);

        return redirect(route('materials.index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        material::where('id', $request->id)->delete();

        return redirect(route('materials.index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);
    }
}