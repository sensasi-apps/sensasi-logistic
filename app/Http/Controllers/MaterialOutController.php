<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;

use Auth;

class MaterialOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('material_outs.index');
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
            'type' => 'required',
            'note' => 'nullable',
            'desc' => 'required',
            'at' => 'required',

            'material_ids' => 'required|array',
            'material_ids.*' => 'required|numeric|min:0|distinct',
        ]);

        $validatedInput['created_by_user_id'] = Auth::user()->id;
        $validatedInput['last_updated_by_user_id'] = Auth::user()->id;

        $materialIn = MaterialOut::create($validatedInput);

        foreach($request->material_ids as $key => $materialId){
            $MaterialOutDetail = new MaterialOutDetail();
            $MaterialOutDetail->material_out_id = $materialIn->id;
            $MaterialOutDetail->mat_in_detail_id = $materialId;
            $MaterialOutDetail->qty = $request->qty[$key];
            $MaterialOutDetail->save();
        }

        return redirect(route('material-outs.index'))->with('message', [
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
            'type' => 'required',
            'note' => 'required',
            'desc' => 'required',
            'at' => 'required'
        ]);

        $validatedInput['last_updated_by_user_id'] = Auth::user()->id;
        $materialOut = MaterialOut::with('detail_outs')->find($id);
        $materialOut->update($validatedInput);

        $materialIds = $materialOut->detail_outs->map(fn ($materialOut) => $materialOut->mat_in_detail_id);
        $toBeDeletedIds = $materialIds->diff($request->mat_in_detail_id);

        $materialOut->detail_outs()->whereIn('mat_in_detail_id', $toBeDeletedIds)->delete();

        foreach($request->material_ids as $key => $materialId){
            if ($materialOutDetail = MaterialOutDetail::find($materialId)) {
                $materialOutDetail->mat_in_detail_id = $request->material_ids[$key];
                $materialOutDetail->qty = $request->qty[$key];
                $materialOutDetail->update();
            } else {
                $materialOutDetail = new MaterialOutDetail();
                $materialOutDetail->material_out_id = $id;
                $materialOutDetail->mat_in_detail_id = $request->material_ids[$key];
                $materialOutDetail->qty = $request->qty[$key];
                $materialOutDetail->save();
            }
        }

        return redirect(route('material-outs.index'))->with('notifications', [[__('Material out data added successfully'), 'success']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MaterialOut::find($id)->delete();
        return redirect(route('material-outs.index'))->with('notifications', [[__('Material out data has been deleted'), 'warning']]);
    }
}
