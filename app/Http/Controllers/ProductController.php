<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private function validateInput(Request $request, int $productId = null)
    {
        return $request->validate([
            'code' => 'nullable|string|unique:mysql.products,code' . ($productId ? ",$productId,id" : null),
            'name' => 'required|string|unique:mysql.products,name' . ($productId ? ",$productId,id" : null),
            'low_qty' => 'numeric',
            'unit' => 'required|string',
            'default_price' => 'required|numeric',
            'tags' => 'nullable|array'
        ]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productInTypes = DB::connection('mysql')->table('product_ins')->select('type')->distinct()->cursor()->pluck('type');
        $productOutTypes = DB::connection('mysql')->table('product_outs')->select('type')->distinct()->cursor()->pluck('type');
        return view('pages.products.index', compact('productInTypes', 'productOutTypes'));
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
        $productFromInput = $this->validateInput($request);
        
        $product = Product::create($productFromInput);

        return redirect(route('products.index'))->with('notifications', [
            [($product->code ?? $product->name) . ' ' . __('was added successfully'), 'success']
        ]);


    }

    public function update(Request $request, Product $product)
    {
        $productFromInput = $this->validateInput($request, $product->id);

        $product->update($productFromInput);

        return redirect(route('products.index'))->with('notifications', [
          [($product->code ?? $product->name) . ' ' . __('was updated successfully'), 'success']
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
        $product = Product::find($id);
        $product->delete();

        return redirect(route('products.index'))->with('notifications', [
          [($product->code ?? $product->name) . ' ' . __('was deleted successfully'), 'warning']
        ]);
    }
}
