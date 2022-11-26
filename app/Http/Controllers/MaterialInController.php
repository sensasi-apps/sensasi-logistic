<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class MaterialInController extends Controller
{
    private function validateInput(Request $request, int $materialInId = null)
    {
        $materialInFromInput = $request->validate([
            'code' => 'nullable|string|unique:mysql.material_ins,code' . ($materialInId ? ",$materialInId,id" : null),
            'type' => 'required|string',
            'note' => 'nullable|string',
            'desc' => 'required|string',
            'at' => 'required|date'
        ]);

        $materialInDetailsFromInput = $request->validate([
            'details' => 'required|array',
            'details.*.material_id' => 'required|exists:mysql.materials,id',
            'details.*.qty' => 'required|integer',
            'details.*.price' => 'required|integer'
        ])['details'];

        $materialInFromInput['last_updated_by_user_id'] = Auth::user()->id;

        return [$materialInFromInput, $materialInDetailsFromInput];
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = DB::connection('mysql')->table('material_ins')->select('type')->distinct()->get()->pluck('type');
        return view('material_ins.index', compact('types'));
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
        [$materialInFromInput, $materialInDetailsFromInput] = $this->validateInput($request);

        $materialInFromInput['created_by_user_id'] = Auth::user()->id;

        if ($materialIn = MaterialIn::create($materialInFromInput)) {
            foreach ($materialInDetailsFromInput as &$materialInDetailFromInput) {
                $materialInDetailFromInput['material_in_id'] = $materialIn->id;
            }

            MaterialInDetail::insert($materialInDetailsFromInput);
        }

        return redirect(route('material-ins.index'))->with('notifications', [
            [__('Material in data has been added successfully'), 'success']
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

    private function getToBeDeletedMaterialIds(MaterialIn $materialIn, array $materialInDetailsFromInput)
    {
        $existsMaterialIds = $materialIn->details->pluck('material_id');
        $materialIdsFromInput = collect($materialInDetailsFromInput)->pluck('material_id');

        return $existsMaterialIds->diff($materialIdsFromInput);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialIn $materialIn)
    {
        [$materialInFromInput, $materialInDetailsFromInput] = $this->validateInput($request, $materialIn->id);

        if ($materialIn->update($materialInFromInput)) {
            foreach ($materialInDetailsFromInput as &$materialInDetailFromInput) {
                $materialInDetailFromInput['material_in_id'] = $materialIn->id;
            }

            $toBeDeletedMaterialIds = $this->getToBeDeletedMaterialIds($materialIn, $materialInDetailsFromInput);

            if ($toBeDeletedMaterialIds->isNotEmpty()) {
                $materialIn
                    ->details()
                    ->whereIn('material_id', $toBeDeletedMaterialIds)
                    ->delete();
            }

            MaterialInDetail::upsert(
                $materialInDetailsFromInput,
                ['material_in_id', 'material_id'],
                ['qty', 'price']
            );
        }

        return redirect(route('material-ins.index'))->with('notifications', [
            [__('Material in data has been updated successfully'), 'success']
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialIn $materialIn)
    {
        $materialIn->delete();
        return redirect(route('material-ins.index'))->with('notifications', [
            [__('Material in data has been deleted'), 'warning']
        ]);
    }
}
