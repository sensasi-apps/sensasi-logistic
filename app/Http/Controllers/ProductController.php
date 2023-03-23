<?php

namespace App\Http\Controllers;

use Helper;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $productFromInput = $this->validateInput($request);

        $product = Product::create($productFromInput);

        return Helper::getSuccessCrudResponse('added', __('product'), $product->id_for_human);
    }

    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $productFromInput = $this->validateInput($request);

        $product->update($productFromInput);

        return Helper::getSuccessCrudResponse('updated', __('product'), $product->id_for_human);
    }

    public function destroy(Product $product): RedirectResponse|JsonResponse
    {
        if ($product->has_children) {
            throw new \Exception('Product has in details');
        }

        $product->delete();

        return Helper::getSuccessCrudResponse('deleted', __('product'), $product->id_for_human);
    }

    private function validateInput(Request $request): array
    {
        $name = $request->name;
        $brand = $request->brand;

        return $request->validate([
            'code' => "nullable|string|unique:mysql.products,code,{$request->id}",
            'name' => [
                'required',
                Rule::unique('products')->where(function ($query) use ($name, $brand) {
                    return $query
                        ->where('name', $name)
                        ->where('brand', $brand);
                })->ignore($request->id)
            ],
            'brand' => 'nullable',
            'low_qty' => 'nullable|numeric',
            'unit' => 'required|string',
            'default_price' => 'required|numeric',
            'tags' => 'nullable|array'
        ]);
    }
}
