<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('products.index');
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
        $validatedInput = $request->validate([
            'name' => 'required|unique:mysql.products',
            'code' => 'nullable|unique:mysql.products',
            'unit' => 'required',
            'default_price' => 'required',
            'tags' => 'nullable|array'
        ]);
        
        Product::create($validatedInput);

        return redirect(route('products.index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $update = $request->validate([
            'code' => 'nullable',
            'name' => 'required',
            'unit' => 'required',
            'default_price' => 'required',
            'tags' => 'nullable|array'
        ]);

        $product->update($update);

        return redirect(route('products.index'))->with('message', [
          'class' => 'success',
          'text' => __(($product->code ?? $product->name) . ' was updated successfully') . '.'
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
        Product::find($id)->delete();

        return redirect(route('products.index'))->with('message', [
          'class' => 'warning',
          'text' => __('Product was deleted successfully') . '.'
        ]);
    }
}
