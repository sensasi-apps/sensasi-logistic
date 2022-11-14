<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use stdClass;

class MaterialInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('material_ins.index');
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

        $materialIn = MaterialIn::create($validatedInput);

        foreach($request->material_ids as $key => $materialId){
            $materialInDetail = new MaterialInDetail();
            $materialInDetail->material_in_id = $materialIn->id;
            $materialInDetail->material_id = $materialId;
            $materialInDetail->qty = $request->qty[$key];
            $materialInDetail->price = $request->price[$key];
            $materialInDetail->save();
        }

        return redirect(route('material-ins.index'))->with('message', [
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
        $materialIn = MaterialIn::with('details')->find($id);
        $materialIn->update($validatedInput);

        $materialIds = $materialIn->map(fn ($materialIn) => $materialIn->material_id);
        $toBeDeletedIds = $materialIds->diff($request->material_id);

        $materialIn->details()->whereIn('material_id', $toBeDeletedIds)->delete();

        foreach($request->material_id as $key => $materialId){
            if ($materialInDetail = MaterialInDetail::find($materialId)) {
                $materialInDetail->material_id = $request->material_id[$key];
                $materialInDetail->qty = $request->qty[$key];
                $materialInDetail->price = $request->price[$key];
                $materialInDetail->update();
            } else {
                $materialInDetail = new MaterialInDetail();
                $materialInDetail->material_in_id = $id;
                $materialInDetail->material_id = $request->material_id[$key];
                $materialInDetail->qty = $request->qty[$key];
                $materialInDetail->price = $request->price[$key];
                $materialInDetail->save();
            }
        }

        return redirect(route('material-ins.index'))->with('notifications', [[__('Material in data added successfully'), 'success']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MaterialIn::find($id)->delete();
        return redirect(route('material-ins.index'))->with('notifications', [[__('Material in data has been deleted'), 'warning']]);
    }
}
