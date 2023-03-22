<?php

namespace App\Http\Controllers;

use App\Models\MaterialManufacture;
use App\Models\ProductManufacture;
use Carbon\Carbon;
use Illuminate\View\View;

class ManufactureReportController extends Controller
{
    public function __invoke(): View
    {
        [$startDate, $endDate] = $this->getDateRange();

        $materialManufactures = MaterialManufacture::with('materialIn.details.material', 'materialOut.details.materialInDetail.material')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->orderBy('at')
            ->get();

        $productManufactures = ProductManufacture::with('productIn.details.product', 'materialOut.details.materialInDetail.material')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->orderBy('at')
            ->get();

        $productManufactureMaterialOutDetailsGroupByMaterial = $productManufactures->reduce(function ($carry, $manufacture) {
            return $carry->merge($manufacture->materialOut->details);
        }, collect([]))
            ->sortBy('materialInDetail.material.name')
            ->groupBy('materialInDetail.material_id');

        $materialManufactureMaterialOutDetailsGroupByMaterial = $materialManufactures->reduce(function ($carry, $manufacture) {
            return $carry->merge($manufacture->materialOut->details);
        }, collect([]))
            ->sortBy('materialInDetail.material.name')
            ->groupBy('materialInDetail.material_id');

        $productInDetailsGroupByProduct = $productManufactures->reduce(function ($carry, $manufacture) {
            return $carry->merge($manufacture->productIn->details);
        }, collect([]))
            ->sortBy('product.name')
            ->groupBy('product_id');

        $materialInDetailsGroupByMaterial = $materialManufactures->reduce(function ($carry, $manufacture) {
            return $carry->merge($manufacture->materialIn->details);
        }, collect([]))
            ->sortBy('material.name')
            ->groupBy('material_id');

        return view('pages.report.manufacture.index', compact(
            'productManufactures',
            'productManufactureMaterialOutDetailsGroupByMaterial',
            'productInDetailsGroupByProduct',

            'materialManufactures',
            'materialManufactureMaterialOutDetailsGroupByMaterial',
            'materialInDetailsGroupByMaterial'
        ));
    }

    private function getDateRange(): array
    {
        if (request()->daterange) {
            $dateRange = explode('_', request()->daterange);

            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
        } else {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        return [$startDate, $endDate];
    }
}
