<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use App\Models\Manufacture;
use App\Models\ProductIn;
use App\Models\ProductInDetail;
use Illuminate\Support\Facades\Auth;

class ManufactureController extends Controller
{
    private function validateInput(Request $request, int $manufactureId = null, int $materialOutId = null, int $productInId = null)
    {
        $manufactureFromInput = $request->validate([
            'manufacture' => 'required|array',
            'manufacture.code' => 'nullable|string|unique:mysql.manufactures,code' . ($manufactureId ? ",$manufactureId,id" : null),
            'manufacture.note' => 'nullable|string',
            'manufacture.at' => 'required|date'
        ])['manufacture'];


        $materialOutFromInput['code'] = $manufactureFromInput['code'];
        $materialOutFromInput['at'] = $manufactureFromInput['at'];
        $materialOutFromInput['type'] = 'Manufacture';
        $materialOutFromInput['last_updated_by_user_id'] = Auth::user()->id;

        $materialOutDetailsFromInput = $request->validate([
            'detailsMaterialOut' => 'required|array',
            'detailsMaterialOut.*.material_in_detail_id' => 'required|exists:mysql.material_in_details,id',
            'detailsMaterialOut.*.qty' => 'required|integer',
        ])['detailsMaterialOut'];

        $productInFromInput['code'] = $manufactureFromInput['code'];
        $productInFromInput['at'] = $manufactureFromInput['at'];
        $productInFromInput['type'] = 'Manufacture';
        $productInFromInput['last_updated_by_user_id'] = Auth::user()->id;

        $productInDetailsFromInput = $request->validate([
            'detailsProductIn' => 'required|array',
            'detailsProductIn.*.product_id' => 'required|exists:mysql.products,id',
            'detailsProductIn.*.qty' => 'required|integer',
        ])['detailsProductIn'];



        if (!$manufactureId) {
            $manufactureFromInput['created_by_user_id'] = Auth::user()->id;
            $materialOutFromInput['created_by_user_id'] = Auth::user()->id;
            $productInFromInput['created_by_user_id'] = Auth::user()->id;
        }


        return [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput];
    }

    public function index()
    {
        return view('pages.manufacture.index');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput] = $this->validateInput($request);


        DB::beginTransaction();

        try {
            if ($materialOut = MaterialOut::create($materialOutFromInput)) {
                foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                    $materialOutDetailFromInput['material_out_id'] = $materialOut->id;
                }

                MaterialOutDetail::insert($materialOutDetailsFromInput);
            }

            if ($productIn = ProductIn::create($productInFromInput)) {
                foreach ($productInDetailsFromInput as &$productInDetailFromInput) {
                    $productInDetailFromInput['product_in_id'] = $productIn->id;
                }

                ProductInDetail::insert($productInDetailsFromInput);
            }

            $manufactureFromInput['material_out_id'] = $materialOut->id;
            $manufactureFromInput['product_in_id'] = $productIn->id;

            Manufacture::create($manufactureFromInput);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with('notifications', [
                [__('Someting went wrong')]
            ]);
        }

        return redirect()->back()->with('notifications', [
            [" <b>" . __('Manufacture data') . "</b> " . __('has been added successfully'), 'success']

        ]);
    }

    private function getToBeDeletedMaterialInDetailIds(Manufacture $Manufacture, array $materialOutDetailsFromInput)
    {
        $existsMaterialInDetailIds = $Manufacture->materialOut->details->pluck('material_in_detail_id');
        $materialInDetailIdsFromInput = collect($materialOutDetailsFromInput)->pluck('material_in_detail_id');

        return $existsMaterialInDetailIds->diff($materialInDetailIdsFromInput);
    }

    private function getToBeDeletedProductIds(Manufacture $Manufacture, array $productInDetailsFromInput)
    {
        $existsProductIds = $Manufacture->productIn->details->pluck('product_id');
        $productIdsFromInput = collect($productInDetailsFromInput)->pluck('product_id');

        return $existsProductIds->diff($productIdsFromInput);
    }

    public function update(Request $request, Manufacture $manufacture)
    {

        [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput] = $this->validateInput($request, $manufacture->id, $manufacture->material_out_id, $manufacture->product_in_id);

        DB::beginTransaction();

        try {
            if ($manufacture->update($manufactureFromInput)) {
                $manufacture->materialOut()->update($materialOutFromInput);
                $manufacture->productIn()->update($productInFromInput);
    
                foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                    $materialOutDetailFromInput['material_out_id'] = $manufacture->material_out_id;
                }
    
                $toBeDeletedMaterialInDetailIds = $this->getToBeDeletedMaterialInDetailIds($manufacture, $materialOutDetailsFromInput);
    
                if ($toBeDeletedMaterialInDetailIds->isNotEmpty()) {
                    $manufacture
                        ->materialOut
                        ->details()
                        ->whereIn('material_in_detail_id', $toBeDeletedMaterialInDetailIds)
                        ->delete();
                }
    
                MaterialOutDetail::upsert(
                    $materialOutDetailsFromInput,
                    ['material_out_id', 'material_in_detail_id'],
                    ['qty']
                );
    
    
                foreach ($productInDetailsFromInput as &$productInDetailFromInput) {
                    $productInDetailFromInput['product_in_id'] = $manufacture->product_in_id;
                }
    
                $toBeDeletedProductIds = $this->getToBeDeletedProductIds($manufacture, $productInDetailsFromInput);
    
                if ($toBeDeletedProductIds->isNotEmpty()) {
    
                    $manufacture
                        ->productIn
                        ->details()
                        ->whereIn('product_id', $toBeDeletedProductIds)
                        ->delete();
                }
    
                ProductInDetail::upsert(
                    $productInDetailsFromInput,
                    ['product_in_id', 'product_id'],
                    ['qty']
                );
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('notifications', [
                [__('Something went wrong')]
            ]);
        }

        return redirect()->back()->with('notifications', [
            [__('Manufacture data') . __('has been updated successfully'), 'success']
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manufacture $manufacture)
    {
        $manufacture->productIn()->delete();
        $manufacture->materialOut()->delete();
        $manufacture->delete();

        $manufatureDate = $manufacture->at->format('d-M-Y');
        
        return redirect()->back()->with('notifications', [
            [__('Manufacture: ') . "$manufatureDate " . __('has been deleted successfully'), 'warning']
        ]);
    }
}
