<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

use App\Models\ProductIn;
use App\Models\ProductInDetail;

class ProductInController extends Controller
{
    private function validateInput(Request $request, int $productInId = null)
    {
        $productInFromInput = $request->validate([
            'code' => 'nullable|string|unique:mysql.product_ins,code,' . ($productInId ? ",$productInId,id" : null),
            'type' => 'required|string',
            'note' => 'nullable|string',
            'desc' => 'required|string',
            'at' => 'required|date'
        ]);

        $productInDetailsFromInput = $request->validate([
            'details' => 'required|array',
            'details.*.product_id' => 'required|exists:mysql.products,id',
            'details.*.qty' => 'required|integer'
        ])['details'];

        $productInFromInput['last_updated_by_user_id'] = Auth::user()->id;

        return [$productInFromInput, $productInDetailsFromInput];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = DB::connection('mysql')->table('product_ins')->select('type')->distinct()->get()->pluck('type');
        return view('pages.product-ins.index', compact('types'));
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
    public function store(Request $request, int $productInId = null)
    {
        [$productInFromInput, $productInDetailsFromInput] = $this->validateInput($request);

        $productInFromInput['created_by_user_id'] = Auth::user()->id;

        if ($productIn = ProductIn::create($productInFromInput)) {
            foreach ($productInDetailsFromInput as &$productInDetailFromInput) {
                $productInDetailFromInput['product_in_id'] = $productIn->id;
            }

            ProductInDetail::insert($productInDetailsFromInput);
        }

        return redirect(route('product-ins.index'))->with('notifications', [
            [__('Product in data has been added successfully'), 'success']
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

    private function getToBeDeletedProductIds(ProductIn $productIn, array $productInDetailsFromInput)
    {
        $existsProductIds = $productIn->details->pluck('product_id');
        $productIdsFromInput = collect($productInDetailsFromInput)->pluck('product_id');

        return $existsProductIds->diff($productIdsFromInput);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductIn $productIn)
    {
        [$productInFromInput, $productInDetailsFromInput] = $this->validateInput($request, $productIn->id);

        if ($productIn->update($productInFromInput)) {
            foreach ($productInDetailsFromInput as &$productInDetailFromInput) {
                $productInDetailFromInput['product_in_id'] = $productIn->id;
            }

            $toBeDeletedProductIds = $this->getToBeDeletedProductIds($productIn, $productInDetailsFromInput);

            if ($toBeDeletedProductIds->isNotEmpty()) {
                $productIn
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

        return redirect(route('product-ins.index'))->with('notifications', [
            [__('Product in data has been updated successfully'), 'success']
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductIn $productIn)
    {
        $productIn->delete();
        return redirect(route('product-ins.index'))->with('notifications', [
            [__('Product in data has been deleted'), 'warning']
        ]);
    }
}
