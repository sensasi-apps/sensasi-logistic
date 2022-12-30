<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\ProductInDetail;
use App\Models\ProductOutDetail;

class ProductOutController extends Controller
{
    private function validateInput(Request $request, int $productOutId = null)
    {
        $productOutFromInput = $request->validate([
            'code' => 'nullable|string|unique:mysql.product_outs,code' . ($productOutId ? ",$productOutId,id" : null),
            'type' => 'required|string',
            'note' => 'nullable|string',
            'at' => 'required|date',
        ]);

        $productOutDetailsFromInput = $request->validate([
            'details' => 'required|array',
            'details.*.product_in_detail_id' => 'required|exists:mysql.product_in_details,id',
            'details.*.qty' => 'required|integer',
            'details.*.price' => 'required|integer'
        ])['details'];

        return [$productOutFromInput, $productOutDetailsFromInput];
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
        [$productOutFromInput, $productOutDetailsFromInput] = $this->validateInput($request);

        if ($productOut = productOut::create($productOutFromInput)) {
            foreach ($productOutDetailsFromInput as &$productOutDetailFromInput) {
                $productOutDetailFromInput['product_out_id'] = $productOut->id;
            }

            ProductOutDetail::insert($productOutDetailsFromInput);
        }

        return redirect()->back()->with('notifications', [
            [__('Product out data has been added successfully'), 'success']
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

    private function getToBeDeletedProductIds(ProductOut $productOut, array $productOutDetailsFromInput)
    {
        $existsProductIds = $productOut->details->pluck('product_id');
        $productIdsFromInput = collect($productOutDetailsFromInput)->pluck('product_id');

        return $existsProductIds->diff($productIdsFromInput);
    }

    public function update(Request $request, ProductOut $productOut)
    {
        [$productOutFromInput, $productOutDetailsFromInput] = $this->validateInput($request, $productOut->id);
        if ($productOut->update($productOutFromInput)) {
            foreach ($productOutDetailsFromInput as &$productOutDetailFromInput) {
                $productOutDetailFromInput['product_out_id'] = $productOut->id;
            }

            $toBeDeletedProductIds = $this->getToBeDeletedProductIds($productOut, $productOutDetailsFromInput);

            if ($toBeDeletedProductIds->isNotEmpty()) {
                $productOut
                    ->details()
                    ->whereIn('product_id', $toBeDeletedProductIds)
                    ->delete();
            }

            ProductOutDetail::upsert(
                $productOutDetailsFromInput,
                ['product_out_id', 'product_in_detail_id'],
                ['qty']
            );
        }

        return redirect()->back()->with('notifications', [
            [__('Product out data has been updated successfully'), 'success']
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductOut $productOut)
    {
        $productOut->delete();
        return redirect()->back()->with('notifications', [
            [__('Product Out data has been deleted'), 'warning']
        ]);
    }
}
