<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrUpdateProductManufactureRequest;
use App\Services\ProductManufactureService;
use Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProductManufactureController extends Controller
{
    public function store(StoreOrUpdateProductManufactureRequest $request, ProductManufactureService $service): RedirectResponse|JsonResponse
    {
        $productManufacture = $service->store($request);

        return Helper::getSuccessCrudResponse('added', __('manufacture'), $productManufacture->id_for_human);
    }

    public function update(StoreOrUpdateProductManufactureRequest $request, ProductManufactureService $service): RedirectResponse|JsonResponse
    {
        $productManufacture = $service->update($request);

        return Helper::getSuccessCrudResponse('updated', __('manufacture'), $productManufacture->id_for_human);
    }

    public function destroy(ProductManufactureService $service): RedirectResponse|JsonResponse
    {
        $productManufacture = $service->destroy();

        return Helper::getSuccessCrudResponse('deleted', __('manufacture'), $productManufacture->id_for_human);
    }
}
