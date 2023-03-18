<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrUpdateMaterialManufactureRequest;
use App\Services\MaterialManufactureService;
use Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaterialManufactureController extends Controller
{
    public function store(StoreOrUpdateMaterialManufactureRequest $request, MaterialManufactureService $service): RedirectResponse|JsonResponse
    {
        $materialManufacture = $service->store($request);

        return Helper::getSuccessCrudResponse('added', __('manufacture'), $materialManufacture->id_for_human);
    }

    public function update(StoreOrUpdateMaterialManufactureRequest $request, MaterialManufactureService $service): RedirectResponse|JsonResponse
    {
        $materialManufacture = $service->update($request);

        return Helper::getSuccessCrudResponse('updated', __('manufacture'), $materialManufacture->id_for_human);
    }

    public function destroy(MaterialManufactureService $service): RedirectResponse|JsonResponse
    {
        $materialManufacture = $service->destroy();

        return Helper::getSuccessCrudResponse('deleted', __('manufacture'), $materialManufacture->id_for_human);
    }
}
