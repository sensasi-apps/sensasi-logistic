<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialOutController extends Controller
{
    private function validateInput(Request $request, int $materialOutId = null)
    {
        $materialOutFromInput = $request->validate([
            'code' => 'nullable|string|unique:mysql.material_outs,code' . ($materialOutId ? ",$materialOutId,id" : null),
            'type' => 'required|string',
            'note' => 'nullable|string',
            'at' => 'required|date'
        ]);

        $materialOutDetailsFromInput = $request->validate([
            'details' => 'required|array',
            'details.*.material_in_detail_id' => 'required|exists:mysql.material_in_details,id',
            'details.*.qty' => 'required|integer',
        ])['details'];

        $materialOutFromInput['last_updated_by_user_id'] = Auth::user()->id;

        return [$materialOutFromInput, $materialOutDetailsFromInput];
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = DB::connection('mysql')->table('material_outs')->select('type')->distinct()->get()->pluck('type');
        return view('pages.material-outs.index', compact('types'));
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
        [$materialOutFromInput, $materialOutDetailsFromInput] = $this->validateInput($request);

        $materialOutFromInput['created_by_user_id'] = Auth::user()->id;

        if ($materialOut = MaterialOut::create($materialOutFromInput)) {
            foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                $materialOutDetailFromInput['material_out_id'] = $materialOut->id;
            }

            MaterialOutDetail::insert($materialOutDetailsFromInput);
        }

        return redirect(route('material-outs.index'))->with('notifications', [
            [__('Material out data has been added successfully'), 'success']

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

    private function getToBeDeletedMaterialInDetailIds(MaterialOut $materialOut, Array $materialOutDetailsFromInput)
    {
        $existsMaterialInDetailIds = $materialOut->details->pluck('material_in_detail_id');
        $materialInDetailIdsFromInput = collect($materialOutDetailsFromInput)->pluck('material_in_detail_id');
        
        return $existsMaterialInDetailIds->diff($materialInDetailIdsFromInput);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialOut $materialOut)
    {
        [$materialOutFromInput, $materialOutDetailsFromInput] = $this->validateInput($request, $materialOut->id);


        if ($materialOut->update($materialOutFromInput)) {
            foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                $materialOutDetailFromInput['material_Out_id'] = $materialOut->id;
            }

            $toBeDeletedMaterialInDetailIds = $this->getToBeDeletedMaterialInDetailIds($materialOut, $materialOutDetailsFromInput);

            if ($toBeDeletedMaterialInDetailIds->isNotEmpty()) {
                $materialOut
                    ->details()
                    ->whereIn('material_in_detail_id', $toBeDeletedMaterialInDetailIds)
                    ->delete();
            }

            MaterialOutDetail::upsert(
                $materialOutDetailsFromInput,
                ['material_Out_id', 'material_in_detail_id'],
                ['qty']
            );
        }

        return redirect(route('material-outs.index'))->with('notifications', [
            [__('Material out data has been updated successfully'), 'success']
        ]);
    }

    private function validateUpdate(Request $request) {
        
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
