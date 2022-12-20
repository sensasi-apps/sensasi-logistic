<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use Illuminate\Support\Facades\Auth;

class MaterialInController extends Controller
{
    private function validateInput(Request $request, int $materialInId = null)
    {
        $materialInFromInput = $request->validate([
            'code' => 'nullable|string|unique:mysql.material_ins,code' . ($materialInId ? ",$materialInId,id" : null),
            'type' => 'required|string',
            'note' => 'nullable|string',
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

        return redirect()->back()->with('notifications', [
            [__('Material in data') . " <b>" . $materialIn->at->format('d-m-Y') . "</b> " . __('has been added successfully'), 'success']
        ]);
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

            // TODO: Check outDetails before delete;
            $deleteQuery = $materialIn->details()->with('outDetails')->whereIn('material_id', $toBeDeletedMaterialIds);
            $toBeDeletedMaterialIds = $deleteQuery->get()->map(fn ($detail) => $detail->outDetails ? null : $detail->material_id );

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

        return redirect()->back()->with('notifications', [
            [__('Material in data ') . " <b>" . $materialIn->at->format('d-m-Y') . "</b> " . __('has been updated successfully'), 'success']
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
        if ($materialIn->outDetails()->count() > 0) {
            return redirect()->back()->with('notifications', [
                [__('Material in data') . " <b>" . $materialIn->at->format('d-m-Y') . "</b> " . __('cannot be deleted. Material(s) has been used'), 'danger']
            ]);
        }


        $materialIn->delete();
        return redirect()->back()->with('notifications', [
            [__('Material in data') . " <b>" . $materialIn->at->format('d-m-Y') . "</b> " . __('has been deleted successfully'), 'warning']
        ]);
    }
}
