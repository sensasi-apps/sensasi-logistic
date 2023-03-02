<?php

namespace App\Http\Controllers;

use App\Models\MaterialIn;
use App\Models\MaterialOut;
use Carbon\Carbon;
use Illuminate\View\View;

class MaterialReportController extends Controller
{
    public function __invoke(): View
    {
        [$startDate, $endDate] = $this->getDateRange();

        $materialIns = MaterialIn::with('details.material')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->orderBy('at')
            ->get();

        $materialInDetailsGroupByMaterial = $materialIns->reduce(function ($carry, $item) {
            return $carry->merge($item->details);
        }, collect([]))
            ->sortBy('material.name')
            ->groupBy('material_id');

        $materialOuts = MaterialOut::with('details.materialInDetail.material')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->orderBy('at')
            ->get();

        $materialOutDetailsGroupByMaterial = $materialOuts->reduce(function ($carry, $item) {
            return $carry->merge($item->details);
        }, collect([]))
            ->sortBy('materialInDetail.material.name')
            ->groupBy('materialInDetail.material_id');

        $reportPageId = 'material';

        $title = __('report.name-report', ['name' => __($reportPageId)]);
        $tabs = [
            'in' => __('report.name-ins', ['name' => __($reportPageId)]),
            'out' => __('report.name-outs', ['name' => __($reportPageId)]),
        ];

        $subtabs = [
            'invoice' => __('by invoice'),
            'item' => __('by item')
        ];

        return view('pages.report.page-template', compact(
            'materialIns',
            'materialInDetailsGroupByMaterial',
            'materialOuts',
            'materialOutDetailsGroupByMaterial',

            'title',
            'reportPageId',
            'tabs',
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
