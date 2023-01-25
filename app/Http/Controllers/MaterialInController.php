<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use Illuminate\Support\Collection;
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

    private function updateMaterialInDetails(MaterialIn $materialIn, Collection $materialInDetailsFromInput)
    {
        $oldMaterialIds = $materialIn->details->pluck('material_id');
        $newMaterialIds = $materialInDetailsFromInput->pluck('material_id');

        // delete section
        $toBeDeletedMaterialIds = $oldMaterialIds->diff($newMaterialIds);
        $toBeDeletedMaterialInDetails = $materialIn->details->whereIn('material_id', $toBeDeletedMaterialIds);

        // validate deleted material in details
        $toBeDeletedMaterialInDetailIds = $toBeDeletedMaterialInDetails->filter(function ($materialInDetail) {
            // if material in detail has out detail, then it cannot be deleted
            return $materialInDetail->outDetails->isEmpty();
        })->pluck('id')->toArray();

        // delete material in details
        if ($toBeDeletedMaterialInDetailIds) {
            MaterialInDetail::destroy($toBeDeletedMaterialInDetailIds);
        }

        // update section
        $toBeUpdatedMaterialIds = $newMaterialIds->intersect($oldMaterialIds);
        $toBeUpdatedMaterialInDetails = $materialIn->details->whereIn('material_id', $toBeUpdatedMaterialIds)->keyBy('material_id');

        // validate updated material in details
        $validatedMaterialInDetailsFromInput = $materialInDetailsFromInput->filter(
            function ($materialInDetailFromInput) use ($toBeUpdatedMaterialInDetails) {
                $materialId = $materialInDetailFromInput['material_id'];
                // if new qty is greater or equal to stock, then it can be updated
                return $materialInDetailFromInput['qty'] >= $toBeUpdatedMaterialInDetails[$materialId]->stock->qty;
            }
        )->toArray();

        // update material in details
        $materialIn->details()->update($validatedMaterialInDetailsFromInput);


        // insert
        // insert new material in details
        $newMaterialsFromInput = collect($materialInDetailsFromInput)->whereIn('material_id', $newMaterialIds->diff($oldMaterialIds))->toArray();
        MaterialInDetail::create($newMaterialsFromInput);
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
        $materialIn->load('details.outDetails', 'details.material', 'details.stock');

        if ($materialIn->update($materialInFromInput)) {
            foreach ($materialInDetailsFromInput as &$materialInDetailFromInput) {
                $materialInDetailFromInput['material_in_id'] = $materialIn->id;
            }

            $this->updateMaterialInDetails($materialIn, collect($materialInDetailsFromInput));
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
