<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Material_ins;
use Auth;

class Material_insController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'code' => 'nullable',
            'type' => 'nullable',
            'note' => 'required',
            'desc' => 'required'
        ]);

        $validatedInput['at'] = date('Y-m-d h:i:s');
        $validatedInput['last_updated_by_user_id'] = Auth::user()->id;
        $validatedInput['created_by_user_id'] = Auth::user()->id;
        Material_ins::create($validatedInput);

        return redirect(route('materials.index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah Material Insert'
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
        $validatedInput = $request->validate([
            'code' => 'nullable',
            'last_updated_by_user_id' => 'required',
            'type' => 'required',
            'note' => 'required',
            'desc' => 'required'
        ]);

        $validatedInput['at'] = date('Y-m-d h:i:s');
        Material_ins::find($id)->update($validatedInput);

        return redirect(route('materials.index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah Material Insert'
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
        Material_ins::find($id)->delete();
        return redirect(route('materials.index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah Material Insert'
        ]);
    }
}
