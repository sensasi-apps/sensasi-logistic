<?php

namespace App\Http\Controllers;

use App\Models\Manufacture;
use Carbon\Carbon;
use Illuminate\View\View;

class ManufactureReportController extends Controller
{
    public function __invoke(): View
    {
        [$startDate, $endDate] = $this->getDateRange();

        $manufactures = Manufacture::with('productIn.details.product', 'materialOut.details.materialInDetail.material')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->orderBy('at')
            ->get();

        $materialOutDetailsGroupByMaterial = $manufactures->reduce(function ($carry, $manufacture) {
            return $carry->merge($manufacture->materialOut->details);
        }, collect([]))
            ->sortBy('materialInDetail.material.name')
            ->groupBy('materialInDetail.material_id');

        $productInDetailsGroupByProduct = $manufactures->reduce(function ($carry, $manufacture) {
            return $carry->merge($manufacture->productIn->details);
        }, collect([]))
            ->sortBy('product.name')
            ->groupBy('product_id');

        $reportPageId = 'manufacture';

        $title = __('report.name-report', ['name' => __('manufacture')]);
        $key = '';
        $tab = $title;

        $subtabs = [
            'invoice' => __('by invoice'),
            'material' => __('by item')
        ];

        return view('pages.report.manufacture', compact(
            'manufactures',
            'materialOutDetailsGroupByMaterial',
            'productInDetailsGroupByProduct',

            'reportPageId',
            'title',
            'key',
            'tab',
            'subtabs'
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
