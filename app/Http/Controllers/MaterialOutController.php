<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use Illuminate\Support\Facades\Auth;

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
        $materialOutFromInput = $request->validate([
            'code' => 'nullable|string|unique:mysql.material_outs',
            'type' => 'required|string',
            'note' => 'nullable|string',
            'desc' => 'required|string',
            'at' => 'required|date'
        ]);

        $materialOutDetailsFromInput = $request->validate([
            'details' => 'required|array',
            'details.*.id' => 'nullable',
            'details.*.material_in_detail_id' => 'required|exists:mysql.material_in_details,id',
            'details.*.qty' => 'required|integer',
        ])['details'];

        $materialOutFromInput['created_by_user_id'] = Auth::user()->id;
        $materialOutFromInput['last_updated_by_user_id'] = Auth::user()->id;

        if ($materialOut = MaterialOut::create($materialOutFromInput)) {
            foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                $materialOutDetailFromInput['material_out_id'] = $materialOut->id;
            }

            MaterialOutDetail::insert($materialOutDetailsFromInput);
        }

        return redirect(route('material-outs.index'))->with('notifications', [
            ['Berhasil menambahkan bahan keluar', 'success']
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
    public function update(Request $request, MaterialOut $MaterialOut)
    {
        // dd($request->all());
        $materialOutFromInput = $request->validate([
            'code' => 'nullable|string|unique:mysql.material_outs,code,id,' . $MaterialOut->id,
            'type' => 'required|string',
            'note' => 'nullable|string',
            'desc' => 'required|string',
            'at' => 'required|date'
        ]);

        $materialOutDetailsFromInput = $request->validate([
            'details' => 'required|array',
            'details.*.material_in_detail_id' => 'required|exists:mysql.material_in_details,id',
            'details.*.qty' => 'required|integer',
        ])['details'];

        $materialOutFromInput['last_updated_by_user_id'] = Auth::user()->id;

        if ($MaterialOut->update($materialOutFromInput)) {
            foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                $materialOutDetailFromInput['material_Out_id'] = $MaterialOut->id;
            }

            $existsMaterialIds = $MaterialOut->details->pluck('material_in_detail_id');
            $materialIdsFromInput = collect($materialOutDetailsFromInput)->pluck('material_in_detail_id');
            $toBeDeletedMaterialIds = $existsMaterialIds->diff($materialIdsFromInput);

            if ($toBeDeletedMaterialIds->isNotEmpty()) {
                $MaterialOut
                    ->details()
                    ->whereIn('material_in_detail_id', $toBeDeletedMaterialIds)
                    ->delete();
            }

            MaterialOutDetail::upsert(
                $materialOutDetailsFromInput,
                ['material_Out_id', 'material_in_detail_id'],
                ['qty']
            );
        }

        return redirect(route('material-outs.index'))->with('notifications', [
            [__('Material out data updated successfully'), 'success']
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
        MaterialOut::find($id)->delete();
        return redirect(route('material-outs.index'))->with('notifications', [[__('Material out data has been deleted'), 'warning']]);
    }
}
