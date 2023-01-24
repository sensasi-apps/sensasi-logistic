<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ProductOut;
use App\Models\ProductInDetail;
use App\Models\ProductOutDetail;
use Illuminate\Support\Facades\DB;

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

        $pidIdsFromInput = collect($productOutDetailsFromInput)->pluck('product_in_detail_id');
        $pids = ProductInDetail::with('product', 'stock', 'productIn')->whereIn('id', $pidIdsFromInput)->get()->keyBy('id');

        DB::beginTransaction();

        try {
            if ($productOut = ProductOut::create($productOutFromInput)) {
                foreach ($productOutDetailsFromInput as &$productOutDetailFromInput) {
                    $pid = $pids[$productOutDetailFromInput['product_in_detail_id']];

                    if ($pid->stock->qty < $productOutDetailFromInput['qty']) {
                        DB::rollback();
                        return redirect()->back()->with('notifications', [
                            [__('Product') . " <b>" . $pid->product->name . " (" . ($pid->productIn->code ?: date_format(date_create($pid->productIn->at), 'D-M-Y')) . ")</b> " . __('is out of stock'), 'danger']
                        ]);
                    }

                    $productOutDetailFromInput['product_out_id'] = $productOut->id;
                }

                ProductOutDetail::insert($productOutDetailsFromInput);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with('notifications', [
                [__('Something went wrong')]
            ]);
        }

        return redirect()->back()->with('notifications', [
            [__('Product out data') . " <b>" . date_format(date_create($productOut->at), 'D-M-Y') . "</b> " . __('has been added successfully'), 'success']

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
        
        $pidIdsFromInput = collect($productOutDetailsFromInput)->pluck('product_in_detail_id');
        $productOut->load('details.ProductInDetail.product', 'details.ProductInDetail.stock', 'details.ProductInDetail.productIn');
        $pids = ProductInDetail::with('product', 'stock', 'productIn')->whereIn('id', $pidIdsFromInput)->get()->keyBy('id');

        DB::beginTransaction();

        try {
            if ($productOut->update($productOutFromInput)) {

                foreach ($productOutDetailsFromInput as &$productOutDetailFromInput) {
                    $pid = $pids[$productOutDetailFromInput['product_in_detail_id']];
                    $pod = $productOut->details->where('product_in_detail_id', $pid->id)->first();

                    if ($pid->stock->qty < $productOutDetailFromInput['qty'] - ($pod ? $pod->qty : 0)) {
                        DB::rollback();
                        return redirect()->back()->with('notifications', [
                            [__('product') . " <b>" . $pid->product->name . " (" . ($pid->productIn->code ?: date_format(date_create($pid->productIn->at), 'D-M-Y')) . ")</b> " . __('is out of stock'), 'danger']
                        ]);
                    }

                    $productOutDetailFromInput['product_out_id'] = $productOut->id;
                }

                $toBeDeletedProductInDetailIds = $this->getToBeDeletedProductIds($productOut, $productOutDetailsFromInput);

                if ($toBeDeletedProductInDetailIds->isNotEmpty()) {
                    $productOut
                        ->details()
                        ->whereIn('product_in_detail_id', $toBeDeletedProductInDetailIds)
                        ->delete();
                }

                ProductOutDetail::upsert(
                    $productOutDetailsFromInput,
                    ['product_out_id', 'product_in_detail_id'],
                    ['qty']
                );
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('notifications', [
                [__('Something went wrong')]
            ]);
        }

        return redirect()->back()->with('notifications', [
            [__('product out data') . " <b>" . date_format(date_create($productOut->at), 'D-M-Y') . "</b> " . __('has been updated successfully'), 'success']

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
