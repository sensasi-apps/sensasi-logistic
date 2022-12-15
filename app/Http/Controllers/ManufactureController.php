<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Auth;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use App\Models\Manufacture;
use App\Models\ProductIn;
use App\Models\ProductInDetail;

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

        $materialOutFromInput = $request->validate([
            'materialOut' => 'required|array',
            'materialOut.code' => 'nullable|string|unique:mysql.material_outs,code' . ($materialOutId ? ",$materialOutId,id" : null),
            'materialOut.note' => 'nullable|string',
            'materialOut.at' => 'required|date'
        ])['materialOut'];

        $materialOutDetailsFromInput = $request->validate([
            'detailsMaterialOut' => 'required|array',
            'detailsMaterialOut.*.material_in_detail_id' => 'required|exists:mysql.material_in_details,id',
            'detailsMaterialOut.*.qty' => 'required|integer',
        ])['detailsMaterialOut'];

        $productInFromInput = $request->validate([
            'productIn' => 'required|array',
            'productIn.code' => 'nullable|string|unique:mysql.product_ins,code' . ($productInId ? ",$productInId,id" : null),
            'productIn.note' => 'nullable|string',
            'productIn.at' => 'required|date'
        ])['productIn'];

        $productInDetailsFromInput = $request->validate([
            'detailsProductIn' => 'required|array',
            'detailsProductIn.*.product_id' => 'required|exists:mysql.products,id',
            'detailsProductIn.*.qty' => 'required|integer',
        ])['detailsProductIn'];

        $materialOutFromInput['last_updated_by_user_id'] = Auth::user()->id;
        $productInFromInput['last_updated_by_user_id'] = Auth::user()->id;

        return [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput];
    }

    public function index()
    {
        $productInTypes = DB::connection('mysql')->table('product_ins')->select('type')->distinct()->cursor()->pluck('type');
        $materialOutTypes = DB::connection('mysql')->table('material_outs')->select('type')->distinct()->cursor()->pluck('type');
        return view('pages.manufacture.index', compact('productInTypes', 'materialOutTypes'));
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
        // dd($request->all());
        [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput] = $this->validateInput($request);

        $materialOutFromInput['type'] = 'Manufacture';
        $materialOutFromInput['created_by_user_id'] = Auth::user()->id;


        if ($materialOut = MaterialOut::create($materialOutFromInput)) {
            foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                $materialOutDetailFromInput['material_out_id'] = $materialOut->id;
            }

            MaterialOutDetail::insert($materialOutDetailsFromInput);
        }

        $productInFromInput['type'] = 'Manufacture';
        $productInFromInput['created_by_user_id'] = Auth::user()->id;
        


        if ($productIn = ProductIn::create($productInFromInput)) {
            foreach ($productInDetailsFromInput as &$productInDetailFromInput) {
                $productInDetailFromInput['product_in_id'] = $productIn->id;
            }

            ProductInDetail::insert($productInDetailsFromInput);
        }

        $manufactureFromInput['created_by_user_id'] = Auth::user()->id;
        $manufactureFromInput['material_out_id'] = $materialOut->id;
        $manufactureFromInput['product_in_id'] = $productIn->id;

        $manufacture = Manufacture::create($manufactureFromInput);

        return redirect()->route('manufactures.index', '#in')->with('notifications', [
            [__('Manufacture data') . " <b>" . "</b> " . __('has been added successfully'), 'success']

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

    private function getToBeDeletedMaterialInDetailIds(Manufacture $Manufacture, array $materialOutDetailsFromInput)
    {
        // dd($Manufacture->materialOut->details->pluck('material_in_detail_id'));
        $existsMaterialInDetailIds = $Manufacture->materialOut->details->pluck('material_in_detail_id');
        $materialInDetailIdsFromInput = collect($materialOutDetailsFromInput)->pluck('material_in_detail_id');

        return $existsMaterialInDetailIds->diff($materialInDetailIdsFromInput);
    }

    private function getToBeDeletedProductIds(Manufacture $Manufacture, array $productInDetailsFromInput)
    {
        // dd($Manufacture->productIn);
        $existsProductIds = $Manufacture->productIn->details->pluck('product_id');
        $productIdsFromInput = collect($productInDetailsFromInput)->pluck('product_id');

        return $existsProductIds->diff($productIdsFromInput);
    }

    public function update(Request $request, Manufacture $manufacture)
    {
        
        [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput] = $this->validateInput($request, $manufacture->id, $manufacture->material_out_id, $manufacture->product_in_id);

        if ($manufacture->update($manufactureFromInput)) {
            MaterialOut::find($manufacture->material_out_id)->update($materialOutFromInput);
            foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                $materialOutDetailFromInput['material_out_id'] = $manufacture->material_out_id;
            }

            $toBeDeletedMaterialInDetailIds = $this->getToBeDeletedMaterialInDetailIds($manufacture, $materialOutDetailsFromInput);

            if ($toBeDeletedMaterialInDetailIds->isNotEmpty()) {
                // dd( $manufacture->materialOut->details());
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
        }

        if ($manufacture->update($manufactureFromInput)) {
            ProductIn::find($manufacture->product_in_id)->update($productInFromInput);
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

        return redirect()->route('manufactures.index', '#ins')->with('notifications', [
            [__('Manufacture data') . __('has been updated successfully'), 'success']

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
        //
    }
}
